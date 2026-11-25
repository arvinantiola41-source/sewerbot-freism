<?php

namespace SewerBot\model;

/**
 * Handles sewer report CRUD and sensor-event auto-logging.
 * Mirrors Kotlin object ReportRepository.kt
 *
 * In the Kotlin version, Firestore and RTDB listeners were used for real-time
 * updates. PHP is request-based, so here we read the RTDB in a single snapshot
 * instead of using a ValueEventListener.
 */
class ReportRepository
{
    private static ?int $lastLoggedIrAdc = null;
    private static ?int $lastLoggedWaterAdc = null;
    private static int $lastLogTimeMs = 0;

    /**
     * Add a report to both Firestore and RTDB.
     */
    public static function addReport(string $location, string $status): void
    {
        $timestamp = date('Y-m-d H:i:s');

        $reportMap = [
            'location' => $location,
            'status' => $status,
            'lastChecked' => $timestamp,
        ];

        // Save to RTDB
        try {
            $rtdb = FirebaseService::getDatabase();
            $rtdb->getReference('reports')->push($reportMap);
        } catch (\Throwable $e) {
            error_log('RTDB report push failed: ' . $e->getMessage());
        }

        // Save to Firestore
        try {
            $firestore = FirebaseService::getFirestore();
            $db = $firestore->database();
            $db->collection('reports')->add($reportMap);
        } catch (\Throwable $e) {
            error_log('Firestore report add failed: ' . $e->getMessage());
        }
    }

    /**
     * Auto-log sensor events if thresholds are crossed.
     * Mirrors Kotlin logSensorEventsIfNeeded().
     *
     * @param int $irAdc
     * @param int $waterAdc
     */
    public static function logSensorEventsIfNeeded(int $irAdc, int $waterAdc): void
    {
        $currentTime = (int)(microtime(true) * 1000);

        // Prevent duplicate spamming logs (10s window between events)
        if ($currentTime - self::$lastLogTimeMs < 10000) {
            return;
        }

        // 1. Water Ingress Detection Log
        if ($waterAdc > 300 && (self::$lastLoggedWaterAdc === null || self::$lastLoggedWaterAdc <= 300)) {
            self::$lastLoggedWaterAdc = $waterAdc;
            self::$lastLogTimeMs = $currentTime;
            self::addReport(
                'Water Ingress Sensor (D35)',
                "🚨 Critical Water Ingress Detected! ($waterAdc ADC)"
            );
            return;
        } elseif ($waterAdc <= 300) {
            self::$lastLoggedWaterAdc = $waterAdc;
        }

        // 2. Sharp IR Sensor Obstacle Detection Log
        if ($irAdc > 1000
            && (self::$lastLoggedIrAdc === null
                || self::$lastLoggedIrAdc <= 1000
                || abs($irAdc - self::$lastLoggedIrAdc) > 500
            )
        ) {
            self::$lastLoggedIrAdc = $irAdc;
            self::$lastLogTimeMs = $currentTime;
            $cm = (int)(13334.0 / ($irAdc + 10.0));
            $cm = max(8, min(80, $cm));
            self::addReport(
                'Sharp IR Distance Sensor (D34)',
                "⚠️ Obstacle Detected! Distance: $cm cm ($irAdc ADC)"
            );
        } elseif ($irAdc <= 1000) {
            self::$lastLoggedIrAdc = $irAdc;
        }
    }

    /**
     * Get all reports from RTDB. Falls back to Firestore if RTDB is empty.
     *
     * @return SewerReport[]
     */
    public static function getReports(): array
    {
        // Try RTDB first
        try {
            $rtdb = FirebaseService::getDatabase();
            $snapshot = $rtdb->getReference('reports')->getSnapshot();
            $value = $snapshot->getValue();

            if (!empty($value)) {
                $reports = [];
                foreach ($value as $id => $data) {
                    $reports[] = new SewerReport(
                        (string)$id,
                        $data['location'] ?? '',
                        $data['status'] ?? '',
                        $data['lastChecked'] ?? ''
                    );
                }
                // Return newest first (reversed), matching Kotlin reports.reversed()
                return array_reverse($reports);
            }
        } catch (\Throwable $e) {
            error_log('RTDB getReports failed: ' . $e->getMessage());
        }

        // Fallback to Firestore
        return self::fetchFirestoreReports();
    }

    /**
     * Fetch reports from Firestore, ordered by lastChecked descending.
     *
     * @return SewerReport[]
     */
    private static function fetchFirestoreReports(): array
    {
        try {
            $firestore = FirebaseService::getFirestore();
            $db = $firestore->database();
            $query = $db->collection('reports')->orderBy('lastChecked', 'DESC');
            $documents = $query->documents();

            $reports = [];
            foreach ($documents as $doc) {
                if (!$doc->exists()) {
                    continue;
                }
                $data = $doc->data();
                $reports[] = new SewerReport(
                    $doc->id(),
                    $data['location'] ?? '',
                    $data['status'] ?? '',
                    $data['lastChecked'] ?? ''
                );
            }
            return $reports;
        } catch (\Throwable $e) {
            error_log('Firestore fetchReports failed: ' . $e->getMessage());
            return [];
        }
    }
}

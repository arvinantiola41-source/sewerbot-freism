<?php

namespace SewerBot\model;

/**
 * Read-only helper to fetch reports from the Firebase Realtime Database.
 * Mirrors Kotlin object RealtimeReportRepository.kt
 *
 * In the Kotlin version this used a ValueEventListener for real-time updates.
 * PHP is request-based, so we read a snapshot in one go.
 */
class RealtimeReportRepository
{
    /**
     * Get all reports from the RTDB "reports" node.
     *
     * @return SewerReport[]
     */
    public static function getReports(): array
    {
        try {
            $rtdb = FirebaseService::getDatabase();
            $snapshot = $rtdb->getReference('reports')->getSnapshot();
            $value = $snapshot->getValue();

            if (empty($value)) {
                return [];
            }

            $reports = [];
            foreach ($value as $id => $data) {
                $reports[] = new SewerReport(
                    (string)$id,
                    $data['location'] ?? '',
                    $data['status'] ?? '',
                    $data['lastChecked'] ?? ''
                );
            }
            return $reports;
        } catch (\Throwable $e) {
            error_log('RealtimeReportRepository getReports failed: ' . $e->getMessage());
            return [];
        }
    }
}

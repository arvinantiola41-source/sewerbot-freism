<?php

namespace SewerBot\model;

/**
 * Handles LED, servo, and movement commands to the Firebase RTDB.
 * Mirrors Kotlin object LedRepository.kt
 *
 * The Kotlin version used ValueEventListener + a Handler watchdog for
 * real-time observation. PHP is request-based, so observeLedData() returns
 * a single snapshot. For real-time updates in the browser you would poll
 * this endpoint via AJAX or use a WebSocket layer.
 */
class LedRepository
{
    // Last-known state (in-process cache, resets per request)
    private static string $lastCommand = 'OFF';
    private static string $lastActualStatus = 'OFF';
    private static string $lastServoCommand = 'OFF';
    private static string $lastServoState = 'CLOSED (0°)';
    private static string $lastMovementCommand = 'STOP';
    private static int $lastIrValue = 0;
    private static int $lastWaterValue = 0;
    private static string $lastDeviceIp = '';

    /**
     * Send an LED command (ON / OFF) to the RTDB.
     *
     * @param string $command "ON" or "OFF"
     * @return array{success:bool, error:?string}
     */
    public static function sendCommand(string $command): array
    {
        try {
            $rtdb = FirebaseService::getDatabase();
            $rtdb->getReference('eldroid/led')->update([
                'command' => $command,
                'lastChangedBy' => 'ANDROID', // kept for device-side compatibility
            ]);
            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage() ?: 'Failed to write command'];
        }
    }

    /**
     * Send a servo command (OPEN/CLOSE) with an angle.
     *
     * @param string $command "OPEN", "ON", "CLOSE", "OFF"
     * @param int $angle
     * @return array{success:bool, error:?string}
     */
    public static function sendServoCommand(string $command, int $angle): array
    {
        $firebaseServoCmd = (strtoupper($command) === 'OPEN' || strtoupper($command) === 'ON') ? 'ON' : 'OFF';

        try {
            $rtdb = FirebaseService::getDatabase();
            $rtdb->getReference('eldroid/servo')->update([
                'command' => $firebaseServoCmd,
                'angle' => $angle,
                'lastChangedBy' => 'ANDROID',
            ]);
            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage() ?: 'Failed to send servo command'];
        }
    }

    /**
     * Send a movement command (FORWARD, BACKWARD, LEFT, RIGHT, STOP).
     *
     * @param string $command
     * @return array{success:bool, error:?string}
     */
    public static function sendMovementCommand(string $command): array
    {
        try {
            $rtdb = FirebaseService::getDatabase();
            $updates = [
                'command' => $command,
                'lastChangedBy' => 'ANDROID',
            ];

            // Write to motor node
            $rtdb->getReference('eldroid/motor')->update($updates);
            // Write to movement node
            $rtdb->getReference('eldroid/movement')->update($updates);

            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage() ?: 'Failed to send movement command'];
        }
    }

    /**
     * Read the entire "eldroid" node from RTDB and return a LedData snapshot.
     *
     * In the Kotlin version this used a ValueEventListener for live updates.
     * Here we return a single snapshot. Call this endpoint repeatedly via
     * AJAX for polling.
     *
     * @return array{data:?LedData, error:?string}
     */
    public static function observeLedData(): array
    {
        try {
            $rtdb = FirebaseService::getDatabase();
            $snapshot = $rtdb->getReference('eldroid')->getSnapshot();
            $eldroid = $snapshot->getValue();

            if ($eldroid === null) {
                return [
                    'data' => new LedData(),
                    'error' => null,
                ];
            }

            $ledNode = $eldroid['led'] ?? [];
            $deviceNode = $eldroid['device'] ?? [];
            $servoNode = $eldroid['servo'] ?? [];
            $motorNode = $eldroid['motor'] ?? [];
            $movementNode = $eldroid['movement'] ?? [];
            $sensorNode = $eldroid['sensor'] ?? [];

            // LED
            self::$lastCommand = $ledNode['command'] ?? 'OFF';
            self::$lastActualStatus = $ledNode['state'] ?? 'OFF';

            // Servo
            self::$lastServoCommand = $servoNode['command'] ?? 'OFF';
            $rawServoState = $servoNode['state'] ?? 'OFF';
            $rawServoAngle = (string)($servoNode['angle'] ?? '0');

            if (strtoupper($rawServoState) === 'ON'
                || strtoupper($rawServoState) === 'OPEN'
                || $rawServoAngle === '180'
            ) {
                self::$lastServoState = 'OPEN (180°)';
            } else {
                self::$lastServoState = 'CLOSED (0°)';
            }

            // Movement
            self::$lastMovementCommand =
                $motorNode['command']
                ?? $motorNode['state']
                ?? $movementNode['command']
                ?? 'STOP';

            // Sensors
            self::$lastIrValue = $sensorNode['irValue'] ?? 0;
            self::$lastWaterValue = $sensorNode['waterValue'] ?? 0;
            self::$lastDeviceIp = $deviceNode['ip'] ?? '';

            // Device status (heartbeat logic simplified for request-based model)
            $rawDeviceStatus = $deviceNode['status'] ?? 'offline';
            $currentLastUpdate = (string)($deviceNode['lastUpdate'] ?? '');
            $now = (int)(microtime(true) * 1000);

            $evaluatedStatus = 'connecting'; // default

            if (strtolower($rawDeviceStatus) === 'online') {
                $evaluatedStatus = 'online';
            } elseif (empty($currentLastUpdate)) {
                $evaluatedStatus = 'offline';
            } else {
                $evaluatedStatus = 'connecting';
            }

            $ledData = new LedData(
                self::$lastCommand,
                self::$lastActualStatus,
                $evaluatedStatus,
                self::$lastServoCommand,
                self::$lastServoState,
                self::$lastMovementCommand,
                self::$lastIrValue,
                self::$lastWaterValue,
                self::$lastDeviceIp
            );

            return ['data' => $ledData, 'error' => null];
        } catch (\Throwable $e) {
            return ['data' => null, 'error' => $e->getMessage()];
        }
    }
}

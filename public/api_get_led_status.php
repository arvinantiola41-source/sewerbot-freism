<?php
session_start();
header('Content-Type: application/json');
// Returns sample data for local testing (no Firebase needed)
echo json_encode([
    'command'         => 'OFF',
    'actualStatus'    => 'OFF',
    'deviceStatus'    => 'offline',
    'servoCommand'    => 'OFF',
    'servoState'      => 'CLOSED (0°)',
    'movementCommand' => 'STOP',
    'irValue'         => 0,
    'waterValue'      => 0,
    'deviceIp'        => '',
]);

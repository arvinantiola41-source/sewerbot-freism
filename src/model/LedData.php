<?php

namespace SewerBot\model;

/**
 * Data class for LED / device state coming from Firebase RTDB.
 * Mirrors Kotlin data class LedData in LedRepository.kt
 */
class LedData
{
    public string $command;
    public string $actualStatus;
    public string $deviceStatus;
    public string $servoCommand;
    public string $servoState;
    public string $movementCommand;
    public int $irValue;
    public int $waterValue;
    public string $deviceIp;

    public function __construct(
        string $command = 'OFF',
        string $actualStatus = 'OFF',
        string $deviceStatus = 'connecting',
        string $servoCommand = 'OFF',
        string $servoState = 'CLOSED (0°)',
        string $movementCommand = 'STOP',
        int $irValue = 0,
        int $waterValue = 0,
        string $deviceIp = ''
    ) {
        $this->command = $command;
        $this->actualStatus = $actualStatus;
        $this->deviceStatus = $deviceStatus;
        $this->servoCommand = $servoCommand;
        $this->servoState = $servoState;
        $this->movementCommand = $movementCommand;
        $this->irValue = $irValue;
        $this->waterValue = $waterValue;
        $this->deviceIp = $deviceIp;
    }
}

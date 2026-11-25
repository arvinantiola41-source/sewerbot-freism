<?php

namespace SewerBot\model;

/**
 * Data class for a sewer inspection report.
 * Mirrors Kotlin data class SewerReport.kt
 */
class SewerReport
{
    public string $id;
    public string $location;
    public string $status;
    public string $lastChecked;

    public function __construct(
        string $id,
        string $location,
        string $status,
        string $lastChecked
    ) {
        $this->id = $id;
        $this->location = $location;
        $this->status = $status;
        $this->lastChecked = $lastChecked;
    }
}

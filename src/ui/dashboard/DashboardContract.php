<?php

namespace SewerBot\ui\dashboard;

use SewerBot\base\BaseView;
use SewerBot\base\BasePresenter;
use SewerBot\model\SewerReport;

/**
 * Contract for the Dashboard screen.
 * Mirrors Kotlin DashboardContract.kt
 */
interface DashboardView extends BaseView
{
    /**
     * @param SewerReport[] $reports
     */
    public function displayReports(array $reports): void;

    public function updateLedUI(string $command, string $actualStatus, string $deviceStatus, int $waterValue): void;
    public function showCommandSuccess(string $message): void;
    public function navigateToChangePassword(): void;
    public function navigateToLogin(): void;
}

interface DashboardPresenterContract extends BasePresenter
{
    public function loadReports(): void;
    public function startObservingLed(): void;
    public function sendLedCommand(string $command): void;
    public function logout(): void;
}

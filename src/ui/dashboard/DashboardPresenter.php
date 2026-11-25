<?php

namespace SewerBot\ui\dashboard;

use SewerBot\model\LedRepository;
use SewerBot\model\ReportRepository;
use SewerBot\model\UserRepository;

/**
 * Presenter for the Dashboard screen.
 * Mirrors Kotlin DashboardPresenter.kt
 */
class DashboardPresenter implements DashboardPresenterContract
{
    private ?DashboardView $view = null;

    public function attachView(object $view): void
    {
        $this->view = $view;
    }

    public function detachView(): void
    {
        $this->view = null;
    }

    public function loadReports(): void
    {
        $reports = ReportRepository::getReports();
        $this->view?->displayReports($reports);
    }

    public function startObservingLed(): void
    {
        $this->view?->showLoading();
        $result = LedRepository::observeLedData();
        $this->view?->hideLoading();

        if ($result['data'] !== null) {
            $ledData = $result['data'];
            $this->view?->updateLedUI(
                $ledData->command,
                $ledData->actualStatus,
                $ledData->deviceStatus,
                $ledData->waterValue
            );
        } elseif ($result['error'] !== null) {
            $this->view?->showError($result['error']);
        }
    }

    public function sendLedCommand(string $command): void
    {
        $this->view?->showLoading();
        $result = LedRepository::sendCommand($command);
        $this->view?->hideLoading();

        if ($result['success']) {
            $this->view?->showCommandSuccess("LED Command $command sent!");
        } else {
            $this->view?->showError($result['error'] ?? 'Failed to send LED command');
        }
    }

    public function logout(): void
    {
        UserRepository::logout();
        $this->view?->navigateToLogin();
    }
}

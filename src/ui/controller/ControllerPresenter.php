<?php

namespace SewerBot\ui\controller;

use SewerBot\model\LedRepository;

/**
 * Presenter for the Controller screen.
 * Mirrors Kotlin ControllerPresenter.kt
 */
class ControllerPresenter implements ControllerPresenterContract
{
    private ?ControllerView $view = null;

    public function attachView(object $view): void
    {
        $this->view = $view;
    }

    public function detachView(): void
    {
        $this->stopObserving();
        $this->view = null;
    }

    public function startObservingMovement(): void
    {
        $this->view?->showLoading();
        $result = LedRepository::observeLedData();
        $this->view?->hideLoading();

        if ($result['data'] !== null) {
            $data = $result['data'];
            $this->view?->updateMovementUI(
                $data->movementCommand,
                $data->deviceStatus,
                $data->irValue,
                $data->waterValue,
                $data->deviceIp
            );
        } elseif ($result['error'] !== null) {
            $this->view?->showError($result['error']);
        }
    }

    public function sendMovementCommand(string $command): void
    {
        $this->view?->showLoading();
        $result = LedRepository::sendMovementCommand($command);
        $this->view?->hideLoading();

        if ($result['success']) {
            $this->view?->showCommandSuccess("Movement command $command sent!");
        } else {
            $this->view?->showError($result['error'] ?? 'Failed to send movement command');
        }
    }

    public function stopObserving(): void
    {
        // No-op in PHP — the Kotlin version removed a Handler watchdog.
        // In PHP each request is independent, so there is no ongoing listener to stop.
    }
}

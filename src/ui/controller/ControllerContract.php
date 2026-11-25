<?php

namespace SewerBot\ui\controller;

use SewerBot\base\BaseView;
use SewerBot\base\BasePresenter;

/**
 * Contract for the Controller screen.
 * Mirrors Kotlin ControllerContract.kt
 */
interface ControllerView extends BaseView
{
    public function updateMovementUI(
        string $lastCommand,
        string $deviceStatus,
        int $irValue,
        int $waterValue,
        string $deviceIp
    ): void;

    public function showCommandSuccess(string $message): void;
    public function navigateBack(): void;
}

interface ControllerPresenterContract extends BasePresenter
{
    public function startObservingMovement(): void;
    public function sendMovementCommand(string $command): void;
    public function stopObserving(): void;
}

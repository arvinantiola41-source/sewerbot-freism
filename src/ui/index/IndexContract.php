<?php

namespace SewerBot\ui\index;

use SewerBot\base\BaseView;
use SewerBot\base\BasePresenter;

/**
 * Contract for the Index (landing) screen.
 * Mirrors Kotlin IndexContract.kt
 */
interface IndexView extends BaseView
{
    public function navigateToLogin(): void;
    public function navigateToRegister(): void;
    public function navigateToDashboard(): void;
}

interface IndexPresenterContract extends BasePresenter
{
    public function checkExistingUserSession(): void;
    public function onGetStartedClicked(): void;
    public function onCreateAccountClicked(): void;
}

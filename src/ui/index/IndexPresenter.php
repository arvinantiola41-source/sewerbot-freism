<?php

namespace SewerBot\ui\index;

use SewerBot\model\UserRepository;

/**
 * Presenter for the Index (landing) screen.
 * Mirrors Kotlin IndexPresenter.kt
 */
class IndexPresenter implements IndexPresenterContract
{
    private ?IndexView $view = null;

    public function attachView(object $view): void
    {
        $this->view = $view;
    }

    public function detachView(): void
    {
        $this->view = null;
    }

    public function checkExistingUserSession(): void
    {
        // App always starts at the Index page (same as Kotlin)
    }

    public function onGetStartedClicked(): void
    {
        $this->view?->navigateToLogin();
    }

    public function onCreateAccountClicked(): void
    {
        $this->view?->navigateToRegister();
    }
}

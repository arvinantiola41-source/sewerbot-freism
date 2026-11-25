<?php

namespace SewerBot\ui\register;

use SewerBot\base\BaseView;
use SewerBot\base\BasePresenter;

/**
 * Contract for the Register screen.
 * Mirrors Kotlin RegisterContract.kt
 */
interface RegisterView extends BaseView
{
    public function onRegisterSuccess(): void;
    public function navigateToLogin(): void;
}

interface RegisterPresenterContract extends BasePresenter
{
    public function register(string $email, string $pass, string $confirmPass): void;
}

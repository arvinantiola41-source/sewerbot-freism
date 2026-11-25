<?php

namespace SewerBot\ui\login;

use SewerBot\base\BaseView;
use SewerBot\base\BasePresenter;

/**
 * Contract for the Login screen.
 * Mirrors Kotlin LoginContract.kt
 */
interface LoginView extends BaseView
{
    public function onLoginSuccess(): void;
    public function navigateToRegister(): void;
    public function navigateToForgotPassword(): void;
}

interface LoginPresenterContract extends BasePresenter
{
    public function login(string $email, string $pass): void;
}

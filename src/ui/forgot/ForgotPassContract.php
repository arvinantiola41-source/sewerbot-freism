<?php

namespace SewerBot\ui\forgot;

use SewerBot\base\BaseView;
use SewerBot\base\BasePresenter;

/**
 * Contract for the Forgot Password screen.
 * Mirrors Kotlin ForgotPassContract.kt
 */
interface ForgotPassView extends BaseView
{
    public function onResetEmailSent(): void;
    public function navigateToLogin(): void;
}

interface ForgotPassPresenterContract extends BasePresenter
{
    public function resetPassword(string $email): void;
}

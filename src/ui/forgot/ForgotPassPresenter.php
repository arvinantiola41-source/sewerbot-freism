<?php

namespace SewerBot\ui\forgot;

use SewerBot\model\UserRepository;

/**
 * Presenter for the Forgot Password screen.
 * Mirrors Kotlin ForgotPassPresenter.kt
 */
class ForgotPassPresenter implements ForgotPassPresenterContract
{
    private ?ForgotPassView $view = null;

    public function attachView(object $view): void
    {
        $this->view = $view;
    }

    public function detachView(): void
    {
        $this->view = null;
    }

    public function resetPassword(string $email): void
    {
        if (empty($email)) {
            $this->view?->showError('Email cannot be empty');
            return;
        }

        $this->view?->showLoading();
        $result = UserRepository::resetPassword($email);
        $this->view?->hideLoading();

        if ($result['success']) {
            $this->view?->onResetEmailSent();
        } else {
            $this->view?->showError($result['error'] ?? 'Failed to send reset email');
        }
    }
}

<?php

namespace SewerBot\ui\register;

use SewerBot\model\UserRepository;

/**
 * Presenter for the Register screen.
 * Mirrors Kotlin RegisterPresenter.kt
 */
class RegisterPresenter implements RegisterPresenterContract
{
    private ?RegisterView $view = null;

    public function attachView(object $view): void
    {
        $this->view = $view;
    }

    public function detachView(): void
    {
        $this->view = null;
    }

    public function register(string $email, string $pass, string $confirmPass): void
    {
        $trimmedEmail = trim($email);

        if (empty($trimmedEmail) || empty($pass) || empty($confirmPass)) {
            $this->view?->showError('All fields are required');
            return;
        }

        if (!str_ends_with(strtolower($trimmedEmail), '@gmail.com')
            || !filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)
        ) {
            $this->view?->showError('Email must be a valid @gmail.com address (e.g. user@gmail.com)');
            return;
        }

        if (!$this->isPasswordValid($pass)) {
            $this->view?->showError('Password must be at least 8 characters, include a digit, an uppercase, a lowercase, and a special character.');
            return;
        }

        if ($pass !== $confirmPass) {
            $this->view?->showError('Passwords do not match!');
            return;
        }

        $this->view?->showLoading();
        $result = UserRepository::register($trimmedEmail, $pass);
        $this->view?->hideLoading();

        if ($result['success']) {
            $this->view?->onRegisterSuccess();
        } else {
            $this->view?->showError($result['error'] ?? 'Registration failed');
        }
    }

    private function isPasswordValid(string $password): bool
    {
        // At least 8 chars, one digit, one lowercase, one uppercase, one special char, no whitespace
        $pattern = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!_])(?=\S+$).{8,}$/';
        return (bool)preg_match($pattern, $password);
    }
}

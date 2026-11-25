<?php

namespace SewerBot\ui\login;

use SewerBot\model\UserRepository;

/**
 * Presenter for the Login screen.
 * Mirrors Kotlin LoginPresenter.kt
 */
class LoginPresenter implements LoginPresenterContract
{
    private ?LoginView $view = null;

    public function attachView(object $view): void
    {
        $this->view = $view;
    }

    public function detachView(): void
    {
        $this->view = null;
    }

    public function login(string $email, string $pass): void
    {
        $trimmedEmail = trim($email);

        if (empty($trimmedEmail) || empty($pass)) {
            $this->view?->showError('Email and password cannot be empty');
            return;
        }

        if (!str_ends_with(strtolower($trimmedEmail), '@gmail.com')
            || !filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)
        ) {
            $this->view?->showError('Email must be a valid @gmail.com address');
            return;
        }

        $this->view?->showLoading();
        $result = UserRepository::login($trimmedEmail, $pass);
        $this->view?->hideLoading();

        if ($result['success']) {
            $this->view?->onLoginSuccess();
        } else {
            $this->view?->showError($result['error'] ?? 'Login failed. Please check your credentials.');
        }
    }
}

<?php

namespace SewerBot\ui\change;

use SewerBot\model\UserRepository;

/**
 * Presenter for the Change Password screen.
 * Mirrors Kotlin ChangePassPresenter.kt
 */
class ChangePassPresenter implements ChangePassPresenterContract
{
    private ?ChangePassView $view = null;

    public function attachView(object $view): void
    {
        $this->view = $view;
    }

    public function detachView(): void
    {
        $this->view = null;
    }

    public function changePassword(string $newPass): void
    {
        if (!$this->isPasswordValid($newPass)) {
            $this->view?->showError('Invalid password format');
            return;
        }

        $this->view?->showLoading();
        $result = UserRepository::updatePassword($newPass);
        $this->view?->hideLoading();

        if ($result['success']) {
            $this->view?->onChangeSuccess();
        } else {
            $this->view?->showError($result['error'] ?? 'Update failed');
        }
    }

    private function isPasswordValid(string $password): bool
    {
        $pattern = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=])(?=\S+$).{8,}$/';
        return (bool)preg_match($pattern, $password);
    }
}

<?php

namespace SewerBot\ui\change;

use SewerBot\base\BaseView;
use SewerBot\base\BasePresenter;

/**
 * Contract for the Change Password screen.
 * Mirrors Kotlin ChangePassContract.kt
 */
interface ChangePassView extends BaseView
{
    public function onChangeSuccess(): void;
}

interface ChangePassPresenterContract extends BasePresenter
{
    public function changePassword(string $newPass): void;
}

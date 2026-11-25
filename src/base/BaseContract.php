<?php

namespace SewerBot\base;

/**
 * Base contract interfaces for the MVP pattern.
 * Mirrors the Kotlin BaseContract.kt
 */
interface BaseView
{
    public function showLoading(): void;
    public function hideLoading(): void;
    public function showError(string $message): void;
}

interface BasePresenter
{
    public function attachView(object $view): void;
    public function detachView(): void;
}

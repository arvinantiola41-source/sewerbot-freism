<?php

namespace SewerBot\model;

/**
 * Data class for a user.
 * Mirrors Kotlin data class User.kt
 */
class User
{
    public string $email;
    public string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}

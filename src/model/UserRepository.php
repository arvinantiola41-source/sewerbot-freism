<?php

namespace SewerBot\model;

use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Exception\Auth\EmailExists;
use Kreait\Firebase\Exception\AuthException;

/**
 * Handles user authentication and profile storage in Firebase Auth, Firestore, and RTDB.
 * Mirrors Kotlin object UserRepository.kt
 */
class UserRepository
{
    /**
     * Register a new user in Firebase Auth, then save the profile to Firestore and RTDB.
     *
     * @param string $email
     * @param string $pass
     * @return array{success:bool, error:?string}
     */
    public static function register(string $email, string $pass): array
    {
        $email = trim($email);

        try {
            $auth = FirebaseService::getAuth();
            $user = $auth->createUser([
                'email' => $email,
                'password' => $pass,
            ]);

            $uid = $user->uid;
            self::saveUserToDatabases($email, $uid);

            return ['success' => true, 'error' => null];
        } catch (EmailExists $e) {
            return ['success' => false, 'error' => 'This email is already registered. Please sign in instead.'];
        } catch (AuthException $e) {
            return ['success' => false, 'error' => self::formatAuthError($e)];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => self::formatAuthError($e)];
        }
    }

    /**
     * Sign in a user with Firebase Auth, then save profile to Firestore and RTDB.
     *
     * @param string $email
     * @param string $pass
     * @return array{success:bool, error:?string}
     */
    public static function login(string $email, string $pass): array
    {
        $email = trim($email);

        try {
            $auth = FirebaseService::getAuth();
            $signInResult = $auth->signInWithEmailAndPassword($email, $pass);
            $uid = $signInResult->firebaseUserId() ?? $email;

            self::saveUserToDatabases($email, $uid);

            // Store the UID in the PHP session so we know who is logged in.
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['uid'] = $uid;
            $_SESSION['email'] = $email;

            return ['success' => true, 'error' => null];
        } catch (UserNotFound $e) {
            return ['success' => false, 'error' => 'Account not found. Please check your email or register a new account.'];
        } catch (InvalidPassword $e) {
            return ['success' => false, 'error' => 'Invalid email or password. Please try again.'];
        } catch (AuthException $e) {
            return ['success' => false, 'error' => self::formatAuthError($e)];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => self::formatAuthError($e)];
        }
    }

    /**
     * Send a password-reset email via Firebase Auth.
     *
     * @param string $email
     * @return array{success:bool, error:?string}
     */
    public static function resetPassword(string $email): array
    {
        try {
            $auth = FirebaseService::getAuth();
            $auth->sendPasswordResetEmail($email);
            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage() ?: 'Failed to send reset email'];
        }
    }

    /**
     * Update the currently logged-in user's password.
     * Firebase Admin SDK cannot directly change a password without the user's
     * current credentials, so this uses the REST API approach or the Admin
     * updateUser method if the UID is known.
     *
     * @param string $newPass
     * @return array{success:bool, error:?string}
     */
    public static function updatePassword(string $newPass): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $uid = $_SESSION['uid'] ?? null;

        if ($uid === null) {
            return ['success' => false, 'error' => 'User not logged in'];
        }

        try {
            $auth = FirebaseService::getAuth();
            $auth->updateUser($uid, ['password' => $newPass]);
            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => self::formatAuthError($e)];
        }
    }

    /**
     * Sign out — clears the PHP session.
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    /**
     * Get the currently logged-in user's email from the session.
     */
    public static function getCurrentUserEmail(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['email'] ?? null;
    }

    // ------------------------------------------------------------------
    //  Private helpers
    // ------------------------------------------------------------------

    private static function formatAuthError(\Throwable $e): string
    {
        $msg = $e->getMessage();
        if (stripos($msg, 'network') !== false || stripos($msg, 'timeout') !== false) {
            return 'Network error. Please check your internet connection.';
        }
        return $msg ?: 'Authentication error occurred';
    }

    private static function saveUserToDatabases(string $email, string $docId): void
    {
        self::saveUserToFirestore($email, $docId);
        self::saveUserToRealtimeDatabase($email, $docId);
    }

    private static function saveUserToFirestore(string $email, string $docId): void
    {
        try {
            $firestore = FirebaseService::getFirestore();
            $db = $firestore->database();
            $db->collection('users')->document($docId)->set([
                'email' => $email,
                'createdAt' => new \DateTimeImmutable(),
                'status' => 'active',
            ]);
        } catch (\Throwable $e) {
            error_log('Firestore write failed: ' . $e->getMessage());
        }
    }

    private static function saveUserToRealtimeDatabase(string $email, string $key): void
    {
        try {
            $rtdb = FirebaseService::getDatabase();
            $sanitizedKey = str_replace('.', '_', $key);
            $rtdb->getReference('users/' . $sanitizedKey)->set([
                'email' => $email,
                'createdAt' => (int)(microtime(true) * 1000),
                'status' => 'active',
            ]);
        } catch (\Throwable $e) {
            error_log('Realtime Database write failed: ' . $e->getMessage());
        }
    }
}

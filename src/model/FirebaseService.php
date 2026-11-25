<?php

namespace SewerBot\model;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Firestore;

/**
 * Centralised Firebase factory so every repository reuses one connection.
 * In Kotlin each object held its own FirebaseDatabase / FirebaseFirestore instance.
 * Here we centralise it to avoid re-initialising the SDK on every request.
 */
class FirebaseService
{
    private static ?Factory $factory = null;
    private static ?Auth $auth = null;
    private static ?Database $rtdb = null;
    private static ?Firestore $firestore = null;

    private const RTDB_URL = 'https://paradelaclinic-default-rtdb.asia-southeast1.firebasedatabase.app';

    private static function getFactory(): Factory
    {
        if (self::$factory === null) {
            // Path to your Firebase service-account JSON file.
            // Download it from Firebase Console → Project Settings → Service Accounts → Generate New Private Key.
            $credentialsPath = __DIR__ . '/../../config/firebase-credentials.json';

            self::$factory = (new Factory())
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri(self::RTDB_URL);
        }
        return self::$factory;
    }

    public static function getAuth(): Auth
    {
        if (self::$auth === null) {
            self::$auth = self::getFactory()->createAuth();
        }
        return self::$auth;
    }

    public static function getDatabase(): Database
    {
        if (self::$rtdb === null) {
            self::$rtdb = self::getFactory()->createDatabase();
        }
        return self::$rtdb;
    }

    public static function getFirestore(): Firestore
    {
        if (self::$firestore === null) {
            self::$firestore = self::getFactory()->createFirestore();
        }
        return self::$firestore;
    }
}

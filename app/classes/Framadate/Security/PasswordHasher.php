<?php

namespace Framadate\Security;

/**
 * Class PasswordHasher
 *
 * Used to abstract the password hash logic
 *
 * @package Framadate\Security
 */
class PasswordHasher {
    /**
     * Hash a password
     *
     * @param string $password the password to hash.
     * @return false|string the hashed password, or false on failure. The used algorithm, cost and salt are returned as part of the hash.
     */
    public static function hash(string $password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify a password with a hash
     *
     * @param string $password the password to verify
     * @param string $hash the hash to compare.
     * @return bool
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

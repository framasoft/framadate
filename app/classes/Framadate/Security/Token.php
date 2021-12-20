<?php
namespace Framadate\Security;

class Token {
    public const DEFAULT_LENGTH = 64;
    private $time;
    private $value;
    private $length;
    private static $codeAlphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';

    public function __construct($length = self::DEFAULT_LENGTH) {
        $this->length = $length;
        $this->time = time() + TOKEN_TIME;
        $this->value = $this->generate();
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isGone(): bool
    {
        return $this->time < time();
    }

    public function check($value): bool
    {
        return $value === $this->value;
    }

    /**
     * Get a secure token if possible, or a less secure one if not.
     *
     * @param int $length The token length
     * @param bool $crypto_strong If passed, tells if the token is "cryptographically strong" or not.
     * @return string
     */
    public static function getToken(int $length = self::DEFAULT_LENGTH, bool &$crypto_strong = false): string
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            openssl_random_pseudo_bytes(1, $crypto_strong); // Fake use to see if the algorithm used was "cryptographically strong"
            return self::getSecureToken($length);
        }
        return self::getUnsecureToken($length);
    }

    public static function getUnsecureToken(int $length): string
    {
        $string = '';
        mt_srand();
        for ($i = 0; $i < $length; $i++) {
            $string .= self::$codeAlphabet[mt_rand() % strlen(self::$codeAlphabet)];
        }

        return $string;
    }

    /**
     * @author http://stackoverflow.com/a/13733588
     */
    public static function getSecureToken(int $length): string
    {
        $token = "";
        for($i=0;$i<$length;$i++){
            $token .= self::$codeAlphabet[self::crypto_rand_secure(0,strlen(self::$codeAlphabet))];
        }
        return $token;
    }

    private function generate(): string
    {
        return self::getToken($this->length);
    }

    /**
     * @author http://us1.php.net/manual/en/function.openssl-random-pseudo-bytes.php#104322
     *
     * @param int $max
     *
     * @psalm-param 0 $min
     * @psalm-param 0|positive-int $max
     */
    private static function crypto_rand_secure(int $min, $max): int {
        $range = $max - $min;
        // not so random...
        if ($range < 0) {
            return $min;
        }
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd &= $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }
}

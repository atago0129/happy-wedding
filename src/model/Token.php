<?php

namespace acolish\model;


class Token
{
    const DELIMITER = '-';

    const EXPIRE = 3600;

    private $salt;

    private $now;

    public function __construct($salt)
    {
        $this->salt = $salt;
        $this->now = time();
    }

    public function generateTokenString($userId, $time)
    {
        return implode(self::DELIMITER, [$time, $this->makeHash($userId, $time)]);
    }

    public function isValid($userId, $tokenString)
    {
        @list($time, $hash) = explode(self::DELIMITER, $tokenString);
        return $this->now - $time < self::EXPIRE && $this->makeHash($userId, $time) === $hash;
    }

    private function makeHash($userId, $time)
    {
        // 今時 sha1 とか・・・まあ、CSRFトークンだし・・・
        return sha1(implode(":", [$userId, $time, $this->salt]));
    }
}
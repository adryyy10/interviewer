<?php

namespace App\Utils;

class RandomStringGenerator
{
    public function __construct(
        private readonly string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
    }

    public function generate(int $length = 16): string
    {
        $charactersLength = strlen($this->characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $this->characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

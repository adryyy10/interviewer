<?php 

namespace App\Utils;

class RandomStringGenerator
{
    private $characters;

    public function __construct($characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $this->characters = $characters;
    }

    public function generate(int $length = 16): string
    {
        $charactersLength = strlen($this->characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $this->characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function setCharacterPool(string $characters): void
    {
        $this->characters = $characters;
    }
}

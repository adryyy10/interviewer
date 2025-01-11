<?php

namespace App\DataFixtures;

use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Webmozart\Assert\Assert;

class AccessTokenFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $devUser = $this->getReference(UserFixtures::REF_DEV_USER);
        Assert::isInstanceOf($devUser, User::class);

        $accessToken = new AccessToken();
        $accessToken->setValue('eyJhbGciOiJSUzI1NiIsImtpZCI6Ijg5Y2UzNTk4YzQ3M2FmMWJkYTRiZmY5NWU2Yzg3MzY0NTAyMDZmYmEiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI4MjM1MzY5MDIxMzctMzAwaW5lMTBhbzJjZ3UxNTNkbDRiZDBmMm03cWZudGouYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI4MjM1MzY5MDIxMzctMzAwaW5lMTBhbzJjZ3UxNTNkbDRiZDBmMm03cWZudGouYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTgyMDIzMTU1MzMzMDk4Mjk3NzQiLCJlbWFpbCI6ImFkcmlhZmlndWVyZXNnYXJjaWF1a0BnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmJmIjoxNzM2NTIzMzIwLCJuYW1lIjoiYWRyaWEgZmlndWVyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jTHMzaHZzdkE3Yi1DUFlMNUplaEFtWm1qNEg2YnhLd3FRRXAyTmNFcUtmN1lrS3dnPXM5Ni1jIiwiZ2l2ZW5fbmFtZSI6ImFkcmlhIiwiZmFtaWx5X25hbWUiOiJmaWd1ZXJlcyIsImlhdCI6MTczNjUyMzYyMCwiZXhwIjoxNzM2NTI3MjIwLCJqdGkiOiIwMzIxZTI3Y2I4ZWRiN2U0NDc1YmY4OTA4NzM5ZDVkN2NjM2EzN2JmIn0.ov3fGfPSPoqCwfmbgZLlXqR1n88HGHroBvO_w3BjL4O7-KYAvfZhlMKnJfx8_TOp32tNGtH85u64eor9KhRQKjD3VsfaIbV2MG9pYTzSi9Uohk6YhgWNj_A9xrcDEbBh9KZMy5cZd5ospFS8_sSMpHQ76DshQzUjc07emkKwhMDNFSLd79Fd264tYWBL6dBm_SF80DUgutOyA4hK-By7Gl_mj6GaJpjxFpQbeVKBELHxAlffMHZ084ixNmi28O8YN_5hc0bYhVJfFNsprU-3tUguv0dZS9pg3fcirzI4slfNwoU7F-oZR8x6gtVpIeE_ofpdIKwM5g2STjg_f60D3w');
        $accessToken->setUser($devUser);
        $accessToken->isValid(true);

        $manager->persist($accessToken);
        $manager->flush();
    }
}

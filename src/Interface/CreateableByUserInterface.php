<?php

namespace App\Interface;

use App\Entity\User;

interface CreatableByUserInterface
{
    public function setCreatedBy(User $user): self;
}

<?php

namespace App\Entity;

use App\Entity\ValueObject\Identity;

class Learner
{
    public int $id;
    public Identity $identity;
    public string $email;

    public function __construct(int $id, Identity $identity, string $email)
    {
        $this->id = $id;
        $this->identity = $identity;
        $this->email = $email;
    }
}

<?php

namespace App\Entity;

use App\Entity\ValueObject\Identity;

class Instructor
{
    public int $id;
    public Identity $identity;

    public function __construct(int $id, Identity $identity)
    {
        $this->id = $id;
        $this->identity = $identity;
    }
}

<?php

namespace App\Context;

use App\Entity\Learner;
use App\Helper\SingletonTrait;

class ApplicationContext
{
    use SingletonTrait;

    /**
     * @var Learner
     */
    private Learner $currentUser;

    protected function __construct()
    {
    }

    public function getCurrentUser(): Learner
    {
        return $this->currentUser;
    }

    public function setCurrentUser(Learner $currentUser): void
    {
        $this->currentUser = $currentUser;
    }
}

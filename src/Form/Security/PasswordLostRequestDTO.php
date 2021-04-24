<?php

declare(strict_types=1);

namespace App\Form\Security;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordLostRequestDTO
{
    /**
     * @Assert\Length(min=0, minMessage="username trop court")
     */
    public ?string $username;
}

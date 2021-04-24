<?php

declare(strict_types=1);

namespace App\Form\Security;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordLostResetDTO
{
    /**
     * @Assert\Length(
     *     min=7,
     *     minMessage="Mot de passe trop court",
     *     max=32,
     *     maxMessage="Mot de passe trop long"
     * )
     */
    public ?string $password;
}

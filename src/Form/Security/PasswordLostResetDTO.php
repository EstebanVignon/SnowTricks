<?php

declare(strict_types=1);

namespace App\Form\Security;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordLostResetDTO
{
    /**
     * @Assert\Length(
     *     min=8,
     *     minMessage="mot de passe trop court, minimum 8 caractères",
     *     max=20,
     *     maxMessage="Mot de passe trop long, max 20 caractères"
     * )
     */
    public ?string $password;
}

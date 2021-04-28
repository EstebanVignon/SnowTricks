<?php

declare(strict_types=1);

namespace App\Form\Security;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordLostRequestDTO
{
    /**
     * @Assert\Length(
     *     min=6,
     *     minMessage="Nom d'utilisateur trop court, minimum 6 caractères",
     *     max=20,
     *     maxMessage="Nom d'utilisateur trop long, maximum 20 caractères"
     * )
     */
    public ?string $username;
}

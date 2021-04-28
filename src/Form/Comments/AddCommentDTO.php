<?php

declare(strict_types=1);

namespace App\Form\Comments;

use Symfony\Component\Validator\Constraints as Assert;

class AddCommentDTO
{
    /**
     * @Assert\Length(
     *     min=12,
     *     minMessage="Commentaire trop court, min 12 caractères",
     *     max=280,
     *     maxMessage="Commentaire trop long, max 280 caractères"
     * )
     */
    public ?string $content;
}

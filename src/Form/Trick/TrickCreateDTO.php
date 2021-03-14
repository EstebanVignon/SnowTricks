<?php

declare(strict_types=1);

namespace App\Form\Trick;

use App\Common\Constraints\UniqueEntityConstraint;
use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntityConstraint(
 *     message="Le nom du trick doit être unique",
 *     fields={"title"},
 *     className="App\Entity\Trick"
 * )
 */
class TrickCreateDTO
{
    /**
     * @Assert\Length(max=8, maxMessage="titre trop long")
     */
    public ?string $title;
    public ?string $description;
    public ?string $mainPicture;
    public ?Category $category;
}
<?php

declare(strict_types=1);

namespace App\Form\Trick;

use App\Common\Constraints\UniqueEntityConstraint;
use App\Entity\Category;
use App\Entity\Trick;

/**
 * @UniqueEntityConstraint(
 *     message="Le nom du trick doit être unique",
 *     fields={"title"},
 *     className="App\Entity\Trick"
 * )
 */
class TrickEditDTO
{
    //public ?string $title;
    public ?string $title;
    public ?string $description;
    public ?string $mainPicture;
    public ?Category $category;

    public static function createFromEntity(Trick $entity): TrickEditDTO
    {
        $trick = new static;

        $trick->title = $entity->getTitle();
        $trick->description = $entity->getDescription();
        $trick->mainPicture = $entity->getMainPicture();
        $trick->category = $entity->getCategory();

        return $trick;
    }
}
<?php

declare(strict_types=1);

namespace App\Form\Trick;

use App\Entity\Trick;

class TrickEditDTO
{
    public $title;
    public $description;
    public $mainPicture;
    public $category;

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
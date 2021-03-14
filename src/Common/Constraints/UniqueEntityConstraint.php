<?php

declare(strict_types=1);

namespace App\Common\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEntityConstraint extends Constraint
{
    public string $message = 'Objet déjà existant';

    public array $fields = [];

    public string $className;

    public function getRequiredOptions(): array
    {
        return ['fields', 'className'];
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
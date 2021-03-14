<?php

declare(strict_types=1);


namespace App\Common\Constraints;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityConstraintValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!is_array($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (count($constraint->fields) === 0) {
            throw new ConstraintDefinitionException('One field must be define');
        }

        if (is_null($constraint->className)) {
            throw new ConstraintDefinitionException('Classname must be define');
        }

        $accessor = new PropertyAccessor();
        foreach ($constraint->fields as $fieldName) {
            $fieldValue = $accessor->getValue($value, $fieldName);
            $object = $this->em->getRepository($constraint->className)
                ->findOneBy(
                    [
                        $fieldName => $fieldValue
                    ]
                );
            if ($object && $this->context->getViolations()->count() === 0) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
<?php

namespace App\Http\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    protected ValidatorInterface $validator;

    /**
     * Validator constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $object)
    {
        $violations = $this->validator->validate($object);

        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }
    }

}
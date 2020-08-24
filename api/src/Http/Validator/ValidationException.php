<?php

namespace App\Http\Validator;

use Throwable;
use LogicException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends LogicException
{
    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations,
                                $message = "Invalid input",
                                $code = 0,
                                Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
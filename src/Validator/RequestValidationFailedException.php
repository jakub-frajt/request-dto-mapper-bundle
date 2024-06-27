<?php
declare(strict_types=1);

namespace JakubFrajt\RequestDtoMapperBundle\Validator;

class RequestValidationFailedException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly array $errors = [],
        \Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}
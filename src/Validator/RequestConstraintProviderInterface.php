<?php
declare(strict_types=1);

namespace JakubFrajt\RequestDtoMapperBundle\Validator;

interface RequestConstraintProviderInterface
{
    public function getConstraints(): array;

    public function supports(string $requestDtoClassName): bool;
}
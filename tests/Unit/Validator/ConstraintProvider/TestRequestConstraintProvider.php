<?php
declare(strict_types=1);

namespace JakubFrajt\RequestDtoMapperBundle\Tests\Unit\Validator\ConstraintProvider;

use JakubFrajt\RequestDtoMapperBundle\Validator\RequestConstraintProviderInterface;

final class TestRequestConstraintProvider implements RequestConstraintProviderInterface
{
    public function __construct(
        private readonly array $constrains,
        private readonly bool $supports
    ) {
    }

    public function getConstraints(): array
    {
        return $this->constrains;
    }

    public function supports(string $requestDtoClassName): bool
    {
        return $this->supports;
    }

}
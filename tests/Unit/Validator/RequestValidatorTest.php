<?php

namespace JakubFrajt\RequestDtoMapperBundle\Tests\Unit\Validator;

use JakubFrajt\RequestDtoMapperBundle\Tests\Unit\Validator\ConstraintProvider\TestRequestConstraintProvider;
use JakubFrajt\RequestDtoMapperBundle\Validator\RequestValidationFailedException;
use JakubFrajt\RequestDtoMapperBundle\Validator\RequestValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

#[CoversClass(RequestValidator::class)]
class RequestValidatorTest extends TestCase
{
    public function testShouldValidateDataWithOneConstraintProvider(): void
    {
        // arrange
        $constraintsProvider = new TestRequestConstraintProvider(
            [
                'name' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ],
            true
        );

        $resourceRequestValidator = new RequestValidator(
            Validation::createValidator(),
            [$constraintsProvider]
        );

        // act
        $resourceRequestValidator->validate(['name' => 'John Foo'], \stdClass::class);

        // assert
        $this->expectNotToPerformAssertions();
    }

    public function testShouldThrowExceptionForInvalidDataWithOneConstraintProvider(): void
    {
        // arrange & setup expectations
        $constraintsProvider = new TestRequestConstraintProvider(
            [
                'name' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ],
            true
        );

        $resourceRequestValidator = new RequestValidator(
            Validation::createValidator(),
            [$constraintsProvider]
        );

        $this->expectException(RequestValidationFailedException::class);

        // act
        $resourceRequestValidator->validate(['name' => null], \stdClass::class);
    }

    public function testShouldValidateDataWithMultipleConstraintProviders(): void
    {
        // arrange
        $constraintsProvider = new TestRequestConstraintProvider(
            [
                'name' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ],
            true
        );

        $anotherConstrainsProvider = new TestRequestConstraintProvider(
            [
                'id' => [
                    new NotBlank(),
                ],
            ],
            true
        );

        $resourceRequestValidator = new RequestValidator(
            Validation::createValidator(),
            [$constraintsProvider, $anotherConstrainsProvider]
        );

        // act
        $resourceRequestValidator->validate(
            [
                'id'   => 1,
                'name' => 'John Foo',
            ],
            \stdClass::class
        );

        // assert
        $this->expectNotToPerformAssertions();
    }

    public function testShouldThrowExceptionForInvalidDataWithMultipleConstraintProviders(): void
    {
        // arrange & setup expectations
        $constraintsProvider = new TestRequestConstraintProvider(
            [
                'name' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ],
            true
        );

        $anotherConstrainsProvider = new TestRequestConstraintProvider(
            [
                'id' => [
                    new NotBlank(),
                ],
            ],
            true
        );

        $resourceRequestValidator = new RequestValidator(
            Validation::createValidator(),
            [$constraintsProvider, $anotherConstrainsProvider]
        );

        $this->expectException(RequestValidationFailedException::class);

        // act
        $resourceRequestValidator->validate(['name' => 'John', 'id' => null], \stdClass::class);
    }

    public function testShouldThrowExceptionForInvalidDataWithSupportedConstraintProvider(): void
    {
        // arrange & setup expectations
        $constraintsProvider = new TestRequestConstraintProvider(
            [
                'name' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
            ],
            false
        );

        $anotherConstrainsProvider = new TestRequestConstraintProvider(
            [
                'id' => [
                    new NotBlank(),
                ],
            ],
            true
        );

        // act & assert
        try {
            $resourceRequestValidator = new RequestValidator(
                Validation::createValidator(),
                [$constraintsProvider, $anotherConstrainsProvider]
            );

            $resourceRequestValidator->validate(['name' => 123, 'id' => null], \stdClass::class);

        } catch (RequestValidationFailedException $e) {
            $this->assertNotEmpty($e->errors);
            $this->assertEquals([
                [
                    'fieldName' => 'id',
                    'errors'    => ['This value should not be blank.'],
                ],
            ], $e->errors);
        }
    }
}

<?php

namespace JakubFrajt\RequestDtoMapperBundle\Tests\Unit\Validator;

use JakubFrajt\RequestDtoMapperBundle\Validator\ViolationListToArrayFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Validation;

#[CoversClass(ViolationListToArrayFormatter::class)]
class ViolationListToArrayFormatterTest extends TestCase
{
    public function testShouldFormatViolationsToArray(): void
    {
        // arrange
        $data = [
            'email' => 'john.foo',
        ];

        $validator = Validation::createValidator();

        $constraints = new Collection([
            'fields' => [
                'email' => new Required([
                    new Email(),
                ]),
            ],
        ]);

        // act
        $validationResult = $validator->validate($data, $constraints);

        // assert
        $this->assertEquals([
            [
                'fieldName' => 'email',
                'errors'    => [
                    'This value is not a valid email address.',
                ],
            ],
        ], ViolationListToArrayFormatter::formatToArray($validationResult));
    }

    public function testShouldFormatSubFieldsWithDot(): void
    {
        // arrange
        $data = [
            'email'   => 'john.foo@localhost',
            'address' => [],
        ];

        $validator = Validation::createValidator();

        $constraints = new Collection([
            'fields' => [
                'email'   => new Required([
                    new Email(),
                ]),
                'address' => new Collection([
                    'fields' => [
                        'street' => new Required([new NotBlank()]),
                    ],
                ]),
            ],
        ]);

        // act
        $validationResult = $validator->validate($data, $constraints);

        // assert
        $this->assertEquals([
            [
                'fieldName' => 'email',
                'errors'    => [
                    'This value is not a valid email address.',
                ],
            ],
            [
                'fieldName' => 'address.street',
                'errors'    => [
                    'This field is missing.',
                ],
            ],
        ], ViolationListToArrayFormatter::formatToArray($validationResult));
    }
}

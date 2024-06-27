<?php
declare(strict_types=1);

namespace JakubFrajt\RequestDtoMapperBundle\Validator;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        /**
         * @var RequestConstraintProviderInterface[]
         */
        #[TaggedIterator(tag: 'jf.request_dto_mapper.request_constraints_provider')]
        private readonly iterable $constraintsProviders
    ) {
    }

    public function validate(array $data, string $requestDtoClassName): void
    {
        $fieldsConstraints = [];

        foreach ($this->constraintsProviders as $constraintsProvider) {
            if ($constraintsProvider->supports($requestDtoClassName)) {
                $fieldsConstraints += $constraintsProvider->getConstraints();
            }
        }

        $validationResult = $this->validator->validate(
            $data,
            new Collection([
                'fields'           => $fieldsConstraints,
                'allowExtraFields' => true,
            ])
        );

        if ($validationResult->count() > 0) {
            throw new RequestValidationFailedException(
                sprintf(
                    'Request validation failed. Request Type "%s".',
                    $requestDtoClassName,
                ),
                ViolationListToArrayFormatter::formatToArray($validationResult)
            );
        }
    }
}
<?php
declare(strict_types=1);

namespace JakubFrajt\RequestDtoMapperBundle\Validator;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationListToArrayFormatter
{
    public static function formatToString(ConstraintViolationListInterface $violationList): string
    {
        assert($violationList instanceof ConstraintViolationList);

        $msg = '';

        foreach ($violationList->getIterator() as $violation) {
            $msg .= ' '.$violation->getPropertyPath().' - '.$violation->getMessage();
        }

        return $msg;
    }

    public static function formatToArray(ConstraintViolationListInterface $violationList): array
    {
        $fieldsErrors = [];

        assert($violationList instanceof ConstraintViolationList);

        foreach ($violationList->getIterator() as $violation) {
            $fieldPath = str_replace('][', '.', trim($violation->getPropertyPath(), '[]'));
            if (isset($fieldsErrors[$fieldPath])
                && in_array(
                    $violation->getMessage(),
                    $fieldsErrors[$fieldPath],
                    true
                )
            ) {
                continue;
            }
            $fieldsErrors[$fieldPath][] = $violation->getMessage();
        }

        $errors = [];

        foreach ($fieldsErrors as $fieldName => $fieldErrors) {
            $errors[] = [
                'fieldName' => $fieldName,
                'errors'    => $fieldErrors,
            ];
        }

        return $errors;
    }
}
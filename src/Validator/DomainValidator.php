<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Util\Validator;

use Optime\Util\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Manuel Aguirre
 */
class DomainValidator
{
    public function __construct(
        private ValidatorInterface $validator,
        private ?TranslatorInterface $translator = null,
    ) {
    }

    public function handle($value, $constraints = null, $groups = null): void
    {
        $errors = $this->validator->validate($value, $constraints, $groups);

        if (0 < count($errors)) {
            $exception = ValidationException::fromValidationErrors($errors);

            if ($this->translator) {
                $exception->setTranslator($this->translator);
            }

            throw $exception;
        }
    }

    public function isValid($value, $constraints = null, $groups = null): bool
    {
        $errors = $this->validator->validate($value, $constraints, $groups);

        return 0 === count($errors);
    }
}

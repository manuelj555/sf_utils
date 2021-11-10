<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Util\Translation\Validation;

use Optime\Util\Translation\EventLanguageConfigurator;
use Optime\Util\Translation\LocalesProviderInterface;
use Optime\Util\Translation\TranslatableContent;
use Optime\Util\Translation\TranslatableContentsAware;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 *
 * @author Manuel Aguirre
 */
class TranslatableConstraintValidator extends ConstraintValidator
{
    /**
     * @var LocalesProviderInterface
     */
    private $localesProvider;

    public function __construct(LocalesProviderInterface $localesProvider)
    {
        $this->localesProvider = $localesProvider;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            // por ahora se ignoran casos de valores nulos.
            return;
        }

        if (!$value instanceof TranslatableContent) {
            throw new \LogicException(sprintf(
                "El constraint TranslatableConstraint solo puede usarse para propiedades de " .
                "tipo TranslatableContent. Sin embargo llegó el tipo '%s' para el atributo '%s'",
                $this->formatTypeOf($value),
                $this->context->getPropertyName()
            ));
        }

        $this->doValidate($value, $constraint);
    }

    private function doValidate(
        TranslatableContent $value,
        Constraint $constraint
    ): void {
        $validator = $this->context->getValidator()->inContext($this->context);

        foreach ($this->localesProvider->getLocales() as $locale) {
            $validator
                ->atPath('values[' . $locale . ']')
                ->validate($value->byLocale($locale), $constraint->constraints);
        }
    }
}
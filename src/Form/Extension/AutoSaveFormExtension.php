<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Util\Form\Extension;

use Optime\Util\Translation\Translation;
use Optime\Util\Translation\TranslationsAwareInterface;
use Optime\Util\Translation\TranslationsFormHandler;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Manuel Aguirre
 */
class AutoSaveFormExtension extends AbstractTypeExtension
{
    public function __construct(
        private TranslationsFormHandler $formHandler,
        private Translation $translation,
    ) {
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('auto_save_translations', false);
        $resolver->setDefault('auto_refresh_data', function (Options $options) {
            return $options['auto_save_translations'];
        });
        $resolver->setAllowedTypes('auto_save_translations', 'bool');
        $resolver->setAllowedTypes('auto_refresh_data', 'bool');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->getCompound() && $options['auto_save_translations']) {
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                if ($event->getForm()->isRoot()) {
                    $this->formHandler->flushAutoSave();
                }
            });
        }

        if ($builder->getCompound() && $options['auto_refresh_data']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                if (!$event->getForm()->isRoot()) {
                    return;
                }

                if (!($data = $event->getData()) instanceof TranslationsAwareInterface) {
                    return;
                }

                $this->translation->refreshInDefaultLocale($data);
            });
        }
    }
}
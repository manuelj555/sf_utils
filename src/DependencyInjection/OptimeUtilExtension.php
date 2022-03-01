<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Util\DependencyInjection;

use Optime\Util\Http\Request\AjaxChecker;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Manuel Aguirre
 */
class OptimeUtilExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('optime.sf_utils.default_locale', $config['default_locale']);
        $container->setParameter('optime.sf_utils.locales', $config['locales']);

        $container
            ->getDefinition(AjaxChecker::class)
            ->setArgument(1, $config['ajax_check']);
    }
}
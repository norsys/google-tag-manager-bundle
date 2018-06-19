<?php
declare (strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

use Norsys\GoogleTagManagerBundle\Config\Resolver as ConfigResolver;
use Norsys\GoogleTagManagerBundle\Exception\DynamicParameterConflictWithStaticParameter;

/**
 * Class NorsysGoogleTagManagerExtension
 */
class NorsysGoogleTagManagerExtension extends BaseExtension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $this->config = $this->processConfiguration($configuration, $configs);

        $mergedPageConfigs = [];

        // Store the default in a pseudo-route named 'default' so we can fallback on it
        // in ConfigResolver when the requested route has no proper config
        $mergedPageConfigs[ConfigResolver::DEFAULTS_KEY] = $this->config['data_layer']['default'];

        foreach (array_keys($this->config['data_layer']['pages']) as $route) {
            $mergedPageConfigs[$route] = $this->mergePageConfigs($route);
        }


        $container->setParameter('norsys_google_tag_manager.pages.configs', $mergedPageConfigs);
        $container->setParameter(
            'norsys_google_tag_manager.parameters.aliases',
            $this->config['data_layer']['aliases']
        );

        $container->setParameter(
            'norsys_google_tag_manager.id',
            $this->config['id']
        );
        $container->setParameter(
            'norsys_google_tag_manager.on_event.enabled',
            $this->config['on_event']['enabled']
        );
        $container->setParameter(
            'norsys_google_tag_manager.on_event.name',
            $this->config['on_event']['name'] ?? ''
        );
        $container->setParameter(
            'norsys_google_tag_manager.on_event.container',
            $this->config['on_event']['container'] ?? ''
        );


        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader  = new YamlFileLoader($container, $locator);
        $loader->load('services.yml');
    }

    /**
     * @param string $route
     *
     * @return array
     * @throws DynamicParameterConflictWithStaticParameter
     */
    private function mergePageConfigs(string $route) : array
    {
        $configDataLayer = $this->config['data_layer'];

        // Resolve static parameters
        $staticParameters = array_merge(
            $configDataLayer['default'][Configuration::CONFIG_STATIC_KEY],
            $configDataLayer['pages'][$route][Configuration::CONFIG_STATIC_KEY] ?? []
        );

        // Resolve dynamic parameters
        $dynamicParameters = array_merge(
            $configDataLayer['default'][Configuration::CONFIG_DYNAMIC_KEY],
            $configDataLayer['pages'][$route][Configuration::CONFIG_DYNAMIC_KEY] ?? []
        );

        // Check there are no name collision between static and dynamic parameters
        foreach ($dynamicParameters as $parameter => $value) {
            if (isset($staticParameters[$parameter]) === true) {
                throw new DynamicParameterConflictWithStaticParameter($parameter);
            }
        }

        return [
            Configuration::CONFIG_STATIC_KEY => $staticParameters,
            Configuration::CONFIG_DYNAMIC_KEY => $dynamicParameters,
        ];
    }
}

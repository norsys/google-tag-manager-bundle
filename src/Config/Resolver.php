<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Config;

use Norsys\GoogleTagManagerBundle\Config\ParameterBag;
use Norsys\GoogleTagManagerBundle\DependencyInjection\Configuration;
use Norsys\GoogleTagManagerBundle\Dynamic\ParameterRegistry as DynamicParameterRegistry;

/**
 * Class Resolver
 */
class Resolver
{
    /**
     * Key for the defaults fake-page
     *
     * @var string
    **/
    const DEFAULTS_KEY = '__DEFAULTS__';

    /**
     * @var DynamicParameterRegistry
     */
    private $dynamicParameterRegistry;

    /**
     * @var array
     */
    private $configPages;

    /**
     * @var array
     */
    private $aliases;

    /**
     * Resolver constructor.
     *
     * @param DynamicParameterRegistry $dynamicParameterRegistry
     * @param array                    $configPages
     * @param array                    $aliases
     */
    public function __construct(DynamicParameterRegistry $dynamicParameterRegistry, array $configPages, array $aliases)
    {
        $this->aliases                  = $aliases;
        $this->configPages              = $configPages;
        $this->dynamicParameterRegistry = $dynamicParameterRegistry;
    }

    /**
     * Get config page
     *
     * @param string $page
     *
     * @return ParameterBag
     */
    public function getConfigPage(string $page): ParameterBag
    {
        // If no config has been defined for the route, use defaults
        if (isset($this->configPages[$page]) === false) {
            $page = self::DEFAULTS_KEY;
        }

        $config = $this->configPages[$page];

        // Resolve dynamic parameters
        $staticParameters = $config[Configuration::CONFIG_STATIC_KEY] ?? [];
        $dynamicParameters = $config[Configuration::CONFIG_DYNAMIC_KEY] ?? [];

        $mergedConfigBag = new ParameterBag(array_merge($staticParameters, $dynamicParameters));

        foreach ($dynamicParameters as $parameter => $value) {
            $resolvedValue = $this->resolveDynamicParameter($parameter, $mergedConfigBag);
            $mergedConfigBag->set($parameter, $resolvedValue);
        }

        return $mergedConfigBag;
    }

    /**
     * Resolve dynamic parameter
     *
     * @param string       $parameterName
     * @param ParameterBag $dynamicBag
     *
     * @return string
     */
    private function resolveDynamicParameter(string $parameterName, ParameterBag $dynamicBag): string
    {
        $parameterName = $this->resolveKey($parameterName);
        $dynamicParameter = $this->dynamicParameterRegistry->get($parameterName);

        return $dynamicParameter->getValue($dynamicBag);
    }

    /**
     * Resolve alias if one found, else return key as-is
     *
     * @param string $alias
     *
     * @return string
     */
    private function resolveKey(string $alias)
    {
        if (true === array_key_exists($alias, $this->aliases)) {
            return $this->aliases[$alias];
        }

        return $alias;
    }
}

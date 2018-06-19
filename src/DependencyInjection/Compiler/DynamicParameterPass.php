<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DynamicParameterPass
 */
class DynamicParameterPass implements CompilerPassInterface
{
    const PARAM_REGISTRY_SERVICE_ID = 'norsys_google_tag_manager.dynamic.parameter_registry';

    const COMPILER_PASS_TAG_NAME = 'norsys_google_tag_manager.dynamic_parameter';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has(self::PARAM_REGISTRY_SERVICE_ID) === false) {
            return;
        }

        $dynamicParameterRegistry = $container->findDefinition(self::PARAM_REGISTRY_SERVICE_ID);

        $dynamicParameters = $container->findTaggedServiceIds(self::COMPILER_PASS_TAG_NAME);

        foreach ($dynamicParameters as $id => $dynamicParameter) {
            $dynamicParameterRegistry->addMethodCall('register', [ new Reference($id) ]);
        }
    }
}

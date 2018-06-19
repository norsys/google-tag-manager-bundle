<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Norsys\GoogleTagManagerBundle\DependencyInjection\Compiler\DynamicParameterPass;

/**
 * Class NorsysGoogleTagManagerBundle
 */
class NorsysGoogleTagManagerBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DynamicParameterPass());
    }
}

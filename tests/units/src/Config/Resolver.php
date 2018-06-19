<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Config\tests\units;

use Norsys\GoogleTagManagerBundle\Config\ParameterBag;
use Tests\Units\Test;
use mock\Norsys\GoogleTagManagerBundle\Dynamic\ParameterRegistry as MockOfDynamicParameterRegistry;
use mock\Norsys\GoogleTagManagerBundle\Dynamic\ParameterInterface as MockOfParameter;

class Resolver extends Test
{
    public function testOnNoConfigForRoute()
    {
        $this
            ->assert('Test if no config page is given.')
            ->given(
                $dynamicParameterRegistry = new MockOfDynamicParameterRegistry,
                $configPages = ['__DEFAULTS__' => ['default']],
                $aliases = [],
                $this->newTestedInstance($dynamicParameterRegistry, $configPages, $aliases),
                $page = 'page_1'
            )
            ->if($result = $this->testedInstance->getConfigPage($page))
            ->then
                ->object($result)
                    ->isEqualTo(new ParameterBag());
    }

    public function testOnExistingConfigForRoute()
    {
        $this
            ->assert('Test behavior for a given configured route with static and dynamic parameters.')
            ->given(
                $dynamicParameterRegistry = new MockOfDynamicParameterRegistry,
                $parameter = new MockOfParameter,
                $this->calling($parameter)->getValue = 'dynamic',
                $this->calling($dynamicParameterRegistry)->get = $parameter,
                $configPages = [
                    'page_2' => [
                        'static' => ['static' => 'static'],
                        'dynamic' => ['dynamic' => 'dynamic']
                    ],
                ],
                $aliases = [],
                $this->newTestedInstance($dynamicParameterRegistry, $configPages, $aliases),
                $page = 'page_2'
            )
            ->if($result = $this->testedInstance->getConfigPage($page))
            ->then
                ->object($result)
                    ->isEqualTo(
                        new ParameterBag(
                            [
                                'static' => 'static',
                                'dynamic' => 'dynamic'
                            ]
                        )
                    );
    }

    public function testOnExistingConfigForRouteWithoutParameter()
    {
        $this
            ->assert('Test behavior for a given configured route without parameters.')
            ->given(
                $dynamicParameterRegistry = new MockOfDynamicParameterRegistry,
                $parameter = new MockOfParameter,
                $configPages = ['page_2' => []],
                $aliases = [],
                $this->newTestedInstance($dynamicParameterRegistry, $configPages, $aliases),
                $page = 'page_2'
            )
            ->if($result = $this->testedInstance->getConfigPage($page))
            ->then
                ->object($result)
                    ->isEqualTo(new ParameterBag());
    }
}

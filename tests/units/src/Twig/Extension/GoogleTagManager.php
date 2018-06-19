<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Twig\Extension\tests\units;

use Tests\Units\Test;
use mock\Norsys\GoogleTagManagerBundle\Config\Resolver as MockOfConfigResolver;
use mock\Norsys\GoogleTagManagerBundle\Config\ParameterBag as MockOfParameterBag;

class GoogleTagManager extends Test
{
    public function testOnGetFunctionsMethod()
    {
        $this
            ->assert('Test getFunctions method return.')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $id = 'XXXX',
                $onEventEnabled = true,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
            ->if($result = $this->testedInstance->getFunctions())
            ->then
                ->array($result)
                ->object($twigFunction = $result[0])
                    ->isInstanceOf(\Twig_SimpleFunction::class)
                    ->if($result1 = $twigFunction->getName())
                    ->then
                        ->string($result1)
                            ->isEqualTo('get_gtm_id')
                ->object($twigFunction = $result[1])
                    ->isInstanceOf(\Twig_SimpleFunction::class)
                    ->if($result2 = $twigFunction->getName())
                    ->then
                        ->string($result2)
                            ->isEqualTo('get_gtm_data_layer')
                ->object($twigFunction = $result[2])
                    ->isInstanceOf(\Twig_SimpleFunction::class)
                    ->if($result3 = $twigFunction->getName())
                    ->then
                        ->string($result3)
                            ->isEqualTo('is_gtm_on_event')
                ->object($twigFunction = $result[3])
                    ->isInstanceOf(\Twig_SimpleFunction::class)
                    ->if($result3 = $twigFunction->getName())
                    ->then
                        ->string($result3)
                            ->isEqualTo('get_gtm_on_event_name')

                ->object($twigFunction = $result[4])
                    ->isInstanceOf(\Twig_SimpleFunction::class)
                    ->if($result3 = $twigFunction->getName())
                    ->then
                        ->string($result3)
                            ->isEqualTo('get_gtm_on_event_container')
            ;
    }

    public function testOnGetGTMMethod()
    {
        $this
            ->assert('Test if page argument is given and page is found on configResolver.')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $pageName = '1',
                $parameterBag = new MockOfParameterBag,
                $this->calling($parameterBag)->all = ['str_\'1', 'str_\2'],
                $this->calling($configResolver)->getConfigPage = function ($page) use ($pageName, $parameterBag) {
                    $this->string($page)->isEqualTo($pageName);
                    return $parameterBag;
                },
                $id = 'XXXX',
                $onEventEnabled = true,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
            ->if($result = $this->testedInstance->getId($pageName))
            ->then
                ->string($result)
                    ->isEqualTo($id);

        $this
            ->assert('Test if page argument is given is null.')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $id = 'XXXX',
                $onEventEnabled = true,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
            ->if($result = $this->testedInstance->getId())
            ->then
                ->string($result)
                    ->isEqualTo($id);
    }


    public function testgetGTMId()
    {
        $this
            ->assert('Test if page argument is given and page is found on configResolver.')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $pageName = '1',
                $parameterBag = new MockOfParameterBag,
                $id = 'XXXXX',
                $onEventEnabled = true,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
                ->if($result = $this->testedInstance->getId())
                ->then
                    ->string($result)
                        ->isEqualTo($id);
        ;
    }

    public function testGTMIsEnabled()
    {
        $this
            ->assert('Test if google tag manager is enabled')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $pageName = '1',
                $parameterBag = new MockOfParameterBag,
                $id = 'XXXXX',
                $onEventEnabled = true,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
                ->if($result = $this->testedInstance->onEventIsEnabled())
                ->then
                    ->boolean($result)
                        ->isEqualTo(true);

        $this
            ->assert('Test if google tag manager is disabled')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $pageName = '1',
                $parameterBag = new MockOfParameterBag,
                $id = 'XXXXX',
                $onEventEnabled = false,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
                ->if($result = $this->testedInstance->onEventIsEnabled())
                ->then
                    ->boolean($result)
                        ->isEqualTo(false);
    }


    public function testGetOnEventName()
    {
        $this
            ->assert('Test if return good event name')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $id = 'XXXXX',
                $onEventEnabled = true,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
            ->if($result = $this->testedInstance->getOnEventName())
            ->then
                ->string($result)
                    ->isEqualTo('my-event');
        $this
            ->assert('Test if return good event name')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $id = 'XXXXX',
                $onEventEnabled = false,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )

            ->then()
            ->exception(function () {
                $this->testedInstance->getOnEventName();
            })
            ->isInstanceOf(\Exception::class)
                ->hasMessage('On event is disable');
        ;
    }

    public function testGetOnEventContainer()
    {
        $this
            ->assert('Test if return good event name')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $id = 'XXXXX',
                $onEventEnabled = true,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )
            ->if($result = $this->testedInstance->getOnEventContainer())
            ->then
                ->string($result)
                    ->isEqualTo('body');
        $this
            ->assert('Test if return good event name')
            ->given(
                $configResolver = new MockOfConfigResolver,
                $id = 'XXXXX',
                $onEventEnabled = false,
                $onEventContainer = 'body',
                $onEventName = 'my-event',
                $this->newTestedInstance($configResolver, $id, $onEventEnabled, $onEventContainer, $onEventName)
            )

            ->then()
            ->exception(function () {
                $this->testedInstance->getOnEventContainer();
            })
            ->isInstanceOf(\Exception::class)
                ->hasMessage('On event is disable');
        ;
    }
}

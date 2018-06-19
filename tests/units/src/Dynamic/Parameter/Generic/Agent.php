<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Dynamic\Parameter\Generic\tests\units;

use Tests\Units\Test;
use mock\Symfony\Component\HttpFoundation\RequestStack as MockOfRequestStack;
use mock\Norsys\GoogleTagManagerBundle\Config\ParameterBag as MockOfParameterBag;
use mock\Symfony\Component\HttpFoundation\Request as MockOfRequest;
use mock\Symfony\Component\HttpFoundation\ServerBag as MockOfServerBag;

class Agent extends Test
{
    public function testOnMobileCase()
    {
        $this
            ->assert('Test for mobile client.')
            ->given(
                $requestStack = new MockOfRequestStack,
                $request = new MockOfRequest,
                $this->calling($requestStack)->getCurrentRequest = $request,
                $serverBag = new MockOfServerBag,
                $agent = 'android',
                $this->calling($serverBag)->get = function ($arg) use ($agent) {
                    if ('HTTP_USER_AGENT' === $arg) {
                        return $agent;
                    }

                    return null;
                },
                $request->server = $serverBag,
                $this->newTestedInstance($requestStack),
                $configPage = new MockOfParameterBag

            )
            ->if($result = $this->testedInstance->getValue($configPage))
            ->then
                ->string($result)
                    ->isEqualTo($this->testedInstance::MOBILE);
    }

    public function testOnDesktopCase()
    {
        $this
            ->assert('Test for desktop client.')
            ->given(
                $requestStack = new MockOfRequestStack,
                $request = new MockOfRequest,
                $this->calling($requestStack)->getCurrentRequest = $request,
                $serverBag = new MockOfServerBag,
                $agent = 'windows',
                $this->calling($serverBag)->get = function ($arg) use ($agent) {
                    if ('HTTP_USER_AGENT' === $arg) {
                        return $agent;
                    }

                    return null;
                },
                $request->server = $serverBag,
                $this->newTestedInstance($requestStack),
                $configPage = new MockOfParameterBag

            )
            ->if($result = $this->testedInstance->getValue($configPage))
                ->then
                    ->string($result)
                ->isEqualTo($this->testedInstance::DESKTOP);
    }

    public function testOnNoClientDetected()
    {
        $this
            ->assert('Test case where no client detected, it is considered as desktop.')
            ->given(
                $requestStack = new MockOfRequestStack,
                $request = new \stdClass(),
                $this->calling($requestStack)->getCurrentRequest = $request,
                $this->newTestedInstance($requestStack),
                $configPage = new MockOfParameterBag
            )
            ->if($result = $this->testedInstance->getValue($configPage))
            ->then
                ->string($result)
                    ->isEqualTo($this->testedInstance::DESKTOP);
    }

    public function testOnGetNameMethod()
    {
        $this
            ->assert('Test getName method.')
            ->given(
                $requestStack = new MockOfRequestStack,
                $this->newTestedInstance($requestStack)
            )
            ->if($result = $this->testedInstance->getName())
            ->then
                ->string($result)
                    ->isEqualTo('agent');
    }
}

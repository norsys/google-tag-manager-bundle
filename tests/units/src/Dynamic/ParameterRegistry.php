<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Dynamic\tests\units;

use Norsys\GoogleTagManagerBundle\Dynamic\ParameterInterface;
use Norsys\GoogleTagManagerBundle\Exception\DynamicParameterNotFound;
use Tests\Units\Test;
use mock\Norsys\GoogleTagManagerBundle\Dynamic\ParameterInterface as MockOfParameter;

class ParameterRegistry extends Test
{
    public function testOnGetMethod()
    {
        $this
            ->assert('Test get method on available key.')
            ->given(
                $parameter = new MockOfParameter,
                $parameterName = 'parameter_1',
                $this->calling($parameter)->getName = $parameterName,
                $this->newTestedInstance(),
                $this->testedInstance->register($parameter)
            )
            ->if($result = $this->testedInstance->get($parameterName))
            ->then
                ->object($result)
                    ->isInstanceOf(ParameterInterface::class)
            ->assert('Try on unavailable key.')
            ->exception(
                function () {
                    $this->testedInstance->get('parameter_2');
                }
            )->isInstanceOf(DynamicParameterNotFound::class);
    }

    public function testOnRegisterMethod()
    {
        $this
            ->assert('Test if method returns this.')
            ->given(
                $this->newTestedInstance(),
                $parameter = new MockOfParameter,
                $parameterName = 'parameter_1',
                $this->calling($parameter)->getName = $parameterName
            )
            ->if($result = $this->testedInstance->register($parameter))
            ->then
                ->object($result)
                    ->isInstanceOf($this->testedInstance)
                    ->isNotEqualTo($this->newTestedInstance());
    }
}

<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Config\tests\units;

use Norsys\GoogleTagManagerBundle\Exception\ParameterNotFound;
use Tests\Units\Test;

class ParameterBag extends Test
{
    public function testOnClearMethod()
    {
        $this
            ->assert('Clear method, clear parameters.')
            ->given(
                $parameters = ['1' => '2'],
                $this->newTestedInstance($parameters)
            )
            ->if($result = $this->testedInstance->clear())
            ->then
                ->object($result)
                    ->isNotEqualTo($this->newTestedInstance($parameters))
            ->if($this->testedInstance->clear())
            ->exception(
                function () {
                    $this->testedInstance->get('1');
                }
            )->isInstanceOf(ParameterNotFound::class);
    }

    public function testOnAddMethod()
    {
        $this
            ->assert('Add method, add parameters.')
            ->given(
                $parameters = ['1' => '2'],
                $this->newTestedInstance($parameters)
            )
            ->if($result = $this->testedInstance->add(['2' => '3']))
            ->then
                ->object($result)
                    ->isNotEqualTo($this->newTestedInstance($parameters))
            ->if($this->testedInstance->add(['2' => '3']))
            ->and($result = $this->testedInstance->all())
            ->then
                ->array($result)
                    ->isEqualTo(['1' => '2', '2' => '3']);
    }

    public function testOnAllMethod()
    {
        $this
            ->assert('All method, get back all results')
            ->given(
                $parameters = ['1' => '2', '3' => '4'],
                $this->newTestedInstance($parameters)
            )
            ->if($result = $this->testedInstance->all())
            ->then
                ->array($result)
                    ->isEqualTo($parameters);
    }

    public function testOnGetMethod()
    {
        $this
            ->assert('Get method, get method get a specific result.')
            ->given(
                $parameters = ['1' => '2'],
                $this->newTestedInstance($parameters)
            )
            ->if($result = $this->testedInstance->get('1'))
            ->then
                ->string($result)
                    ->isEqualTo('2')
            ->exception(
                function () {
                    $this->testedInstance->get('2');
                }
            )->isInstanceOf(ParameterNotFound::class);
    }

    public function testOnSetMethod()
    {
        $this
            ->assert('Test if set method mutate the object.')
            ->given($this->newTestedInstance([]))
            ->if($this->testedInstance->set('1', '2'))
            ->then
                ->object($this->testedInstance)
                    ->isNotEqualTo($this->newTestedInstance())
            ->if($this->testedInstance->set('1', '2'))
            ->and($result = $this->testedInstance->get('1'))
            ->then
                ->string($result)
                    ->isEqualTo('2');
    }

    public function testOnHasMethod()
    {
        $this
            ->assert('Test if has method verify if parameter key exists.')
            ->given(
                $parameters = ['1' => '2'],
                $this->newTestedInstance($parameters)
            )
            ->if($result = $this->testedInstance->has('1'))
            ->then
                ->boolean($result)
                    ->isTrue()
            ->if($result = $this->testedInstance->has('2'))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function testOnRemoveMethod()
    {
        $this
            ->assert('Test if remove method, remove a parameter indexed by a key.')
            ->given(
                $parameter = ['1' => '2'],
                $this->newTestedInstance($parameter)
            )
            ->if($this->testedInstance->remove('1'))
            ->and($result = $this->testedInstance->all())
            ->then
                ->array($result)
                    ->isEmpty()
            ->exception(
                function () {
                    $this->testedInstance->get('1');
                }
            )->isInstanceOf(ParameterNotFound::class);
    }
}

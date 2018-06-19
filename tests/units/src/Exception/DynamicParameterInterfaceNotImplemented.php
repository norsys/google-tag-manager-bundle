<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception\tests\units;

use Tests\Units\Test;
use Norsys\GoogleTagManagerBundle\Exception\DynamicParameterInterfaceNotImplemented as TestedClass;

class DynamicParameterInterfaceNotImplemented extends Test
{
    public function testOnFormatMessage()
    {
        $this
            ->assert('Test format mechanism.')
            ->given(
                $id = 'id_xxx',
                $this->newTestedInstance($id),
                $interfaceFqn = TestedClass::INTERFACE_FQN
            )
            ->if($result = $this->testedInstance->getMessage())
            ->then
                ->string($result)
                    ->isEqualTo('Dynamic parameter service with ID "'.$id.'" must implement "'.$interfaceFqn.'" ');
    }
}

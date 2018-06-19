<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception\tests\units;

use Tests\Units\Test;

class ParameterNotFound extends Test
{
    public function testOnFormatMessage()
    {
        $this
            ->assert('Test format mechanism.')
            ->given(
                $parameter = 'parameter',
                $this->newTestedInstance($parameter)
            )
            ->if($result = $this->testedInstance->getMessage())
            ->then
                ->string($result)
                    ->isEqualTo('Parameter "'.$parameter.'" not found');
    }
}

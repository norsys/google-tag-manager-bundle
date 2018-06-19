<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception\tests\units;

use Tests\Units\Test;

class DynamicParameterNotFound extends Test
{
    public function testOnFormatMessage()
    {
        $this
            ->assert('Test format mechanism.')
            ->given(
                $dynamicParameter = 'i am a dynamic parameter',
                $this->newTestedInstance($dynamicParameter)
            )
            ->if($result = $this->testedInstance->getMessage())
            ->then
                ->string($result)
                    ->isEqualTo('Dynamic parameter "'.$dynamicParameter.'" not found');
    }
}

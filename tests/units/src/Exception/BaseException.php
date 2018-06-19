<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception\tests\units;

use Tests\Units\Test;

class BaseException extends Test
{
    public function testOnFormatMessage()
    {
        $this
            ->assert('Test format mechanism.')
            ->given($this->newTestedInstance())
            ->if($result = $this->testedInstance->getMessage())
            ->then
                ->string($result)
                    ->isEqualTo('Unknown error');
    }
}

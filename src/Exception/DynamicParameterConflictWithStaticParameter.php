<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception;

/**
 * Class DynamicParameterConflictWithStaticParameter
 */
class DynamicParameterConflictWithStaticParameter extends BaseException
{
    /**
     * @var string
     **/
    protected $message = 'Dynamic parameter "{dynamicParameter}" already exist in static parameters';

    /**
     * @param string $dynamicParameter
     **/
    public function __construct(string $dynamicParameter)
    {
        parent::__construct([
            'dynamicParameter' => $dynamicParameter
        ]);
    }
}

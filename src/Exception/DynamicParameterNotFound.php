<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception;

/**
 * Class DynamicParameterNotFound
 */
class DynamicParameterNotFound extends BaseException
{
    /**
     * @var string
     **/
    protected $message = 'Dynamic parameter "{dynamicParameter}" not found';

    /**
     *
     * @param string $dynamicParameter
     **/
    public function __construct(string $dynamicParameter)
    {
        parent::__construct([
            'dynamicParameter' => $dynamicParameter
        ]);
    }
}

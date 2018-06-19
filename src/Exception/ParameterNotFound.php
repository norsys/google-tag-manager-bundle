<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception;

/**
 * Class ParameterNotFound
 */
class ParameterNotFound extends BaseException
{
    /**
     * @var string
     **/
    protected $message = 'Parameter "{parameter}" not found';

    /**
     *
     * @param string $parameter
     **/
    public function __construct(string $parameter)
    {
        parent::__construct([
            'parameter' => $parameter
        ]);
    }
}

<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception;

/**
 * Class DynamicParameterInterfaceNotImplemented
 */
class DynamicParameterInterfaceNotImplemented extends BaseException
{
    const INTERFACE_FQN = 'Norsys\\GoogleTagManagerBundle\\Dynamic\\ParameterInterface';
    /**
     * @var string
     **/
    protected $message = 'Dynamic parameter service with ID "{id}" must implement "{interface}" ';

    /**
     * @param string $id
     **/
    public function __construct(string $id)
    {
        parent::__construct([
            'id' => $id,
            'interface' => self::INTERFACE_FQN,
        ]);
    }
}

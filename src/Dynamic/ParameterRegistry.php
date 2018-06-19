<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Dynamic;

use Norsys\GoogleTagManagerBundle\Exception\DynamicParameterNotFound;

/**
 * Class to provides dynamic parameters
 **/
class ParameterRegistry
{
    /**
     * @var array
     */
    private $dynamicParameters;

    /**
     * DynamicParameterRegistry constructor
     */
    public function __construct()
    {
        $this->dynamicParameters = [];
    }

    /**
     * Get name
     *
     * @param string $name
     *
     * @throws DynamicParameterNotFound
     *
     * @return ParameterInterface
     */
    public function get(string $name): ParameterInterface
    {
        if (isset($this->dynamicParameters[$name]) === false) {
            throw new DynamicParameterNotFound($name);
        }

        return $this->dynamicParameters[$name];
    }

    /**
     * Register dynamic parameter
     *
     * @param ParameterInterface $dynamicParameter
     *
     * @return ParameterRegistry
     */
    public function register(ParameterInterface $dynamicParameter): ParameterRegistry
    {
        $this->dynamicParameters[$dynamicParameter->getName()] = $dynamicParameter;

        return $this;
    }
}

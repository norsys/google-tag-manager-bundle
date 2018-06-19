<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Config;

use Norsys\GoogleTagManagerBundle\Exception\ParameterNotFound;

/**
 * Parameter bag to manage google tags
 **/
class ParameterBag
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Clears all parameters.
     *
     * @return ParameterBag
     */
    public function clear(): ParameterBag
    {
        $this->parameters = [];

        return $this;
    }

    /**
     * Adds parameters
     *
     * @param array $parameters
     *
     * @return ParameterBag
     */
    public function add(array $parameters): ParameterBag
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @throws ParameterNotFound
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        if (isset($this->parameters[$name]) === false) {
            throw new ParameterNotFound($name);
        }

        return $this->parameters[$name] ?? $default;
    }

    /**
     * Sets a service container parameter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return ParameterBag
     */
    public function set(string $name, $value): ParameterBag
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * Removes a parameter.
     *
     * @param string $name
     *
     * @return ParameterBag
     */
    public function remove(string $name): ParameterBag
    {
        unset($this->parameters[$name]);

        return $this;
    }
}

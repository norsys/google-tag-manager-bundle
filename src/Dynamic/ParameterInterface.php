<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Dynamic;

use Norsys\GoogleTagManagerBundle\Config\ParameterBag;

/**
 * Interface to dynamic parameter
 **/
interface ParameterInterface
{
    /**
     * Method responsible for calculating the parameter's value dynamically
     *
     * @param ParameterBag $configPage
     *
     * @return string
     */
    public function getValue(ParameterBag $configPage): string;

    /**
     * The name to be used when refering to the dynamic parameter in the static
     * sub-config section
     *
     * @return string
     */
    public function getName(): string;
}

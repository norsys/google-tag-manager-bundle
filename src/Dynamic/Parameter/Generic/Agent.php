<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Dynamic\Parameter\Generic;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Norsys\GoogleTagManagerBundle\Config\ParameterBag;
use Norsys\GoogleTagManagerBundle\Dynamic\ParameterInterface;

/**
 * Class Agent
 * Return device type upon user-agent detection: mobile or desktop
 */
class Agent implements ParameterInterface
{
    /**
     * @var string
     */
    const DESKTOP = 'desktop';

    /**
     * @var string
     */
    const MOBILE = 'mobile';

    /**
     * @var array
     */
    const MOBILE_AGENTS = [
        'android',
        'avantgo',
        'blackberry',
        'bolt',
        'boost',
        'cricket',
        'docomo',
        'fone',
        'hiptop',
        'mini',
        'mobi',
        'palm',
        'phone',
        'pie',
        'tablet',
        'up.browser',
        'up.link',
        'webos',
        'wos',
    ];

    /**
     * @var string
     **/
    private $userAgent = '';

    /**
     * Agent constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        if ($requestStack->getCurrentRequest() instanceof Request) {
            $this->userAgent = $requestStack->getCurrentRequest()->server->get('HTTP_USER_AGENT');
        }
    }

    /**
     * Get value
     *
     * @param ParameterBag $configPage
     *
     * @return string
     */
    public function getValue(ParameterBag $configPage): string
    {
        $regex = sprintf('/(%s)/', implode('|', self::MOBILE_AGENTS));

        if (preg_match($regex, $this->userAgent) !== 1) {
            return self::DESKTOP;
        }

        return self::MOBILE;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'agent';
    }
}

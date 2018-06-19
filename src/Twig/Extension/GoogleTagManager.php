<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Twig\Extension;

use Norsys\GoogleTagManagerBundle\Config\Resolver as ConfigResolver;
use Norsys\GoogleTagManagerBundle\Exception\OnEventIsDisable;

/**
 * Class GoogleTagManager
 */
class GoogleTagManager extends \Twig_Extension
{
    /**
     * @var ConfigResolver
     */
    private $configResolver;

    /**
     * @var string
     */
    private $id;


    /**
     * @var boolean
     */
    private $onEventEnabled;

    /**
     * @var string
     */
    private $onEventContainer;

    /**
     * @var string
     */
    private $onEventName;

    /**
     * GTMExtension constructor.
     *
     * @param ConfigResolver $configResolver
     * @param string         $id
     * @param boolean        $onEventEnabled
     * @param string         $onEventContainer
     * @param string         $onEventName
     */
    public function __construct(
        ConfigResolver $configResolver,
        string $id,
        bool $onEventEnabled,
        string $onEventContainer,
        string $onEventName
    ) {
        $this->configResolver   = $configResolver;
        $this->id               = $id;
        $this->onEventEnabled   = $onEventEnabled;
        $this->onEventContainer = $onEventContainer;
        $this->onEventName      = $onEventName;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('get_gtm_id', [ $this, 'getId' ]),
            new \Twig_SimpleFunction('get_gtm_data_layer', [ $this, 'getDataLayer' ]),
            new \Twig_SimpleFunction('is_gtm_on_event', [$this, 'onEventIsEnabled']),
            new \Twig_SimpleFunction('get_gtm_on_event_name', [ $this, 'getOnEventName' ]),
            new \Twig_SimpleFunction('get_gtm_on_event_container', [ $this, 'getOnEventContainer' ])
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function onEventIsEnabled(): bool
    {
        return $this->onEventEnabled;
    }

    /**
     * @return string
     */
    public function getOnEventName(): string
    {
        $this->checkOnEventEnabled();

        return $this->onEventName;
    }

    /**
     * @return string
     */
    public function getOnEventContainer(): string
    {
        $this->checkOnEventEnabled();

        return $this->onEventContainer;
    }

    /**
     * @param string $page
     *
     * @return string
     */
    public function getDataLayer(string $page = null): string
    {
        $dataLayer = [];

        if ($page !== null) {
            $dataLayer = $this->configResolver->getConfigPage($page)->all();
        }

        return base64_encode(json_encode($dataLayer));
    }

    /**
     * @throws OnEventIsDisable
     */
    private function checkOnEventEnabled()
    {
        if ($this->onEventIsEnabled() === false) {
            throw new OnEventIsDisable();
        }
    }
}

<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\Exception;

/**
 * Class BaseException
 **/
class BaseException extends \Exception
{

    /**
     * @var array
     **/
    protected $parameters = [];

    /**
     * @var integer
     **/
    protected $code = 500;

    /**
     * @var string
     **/
    protected $message = 'Unknown error';

    /**
     * Constructor
     *
     * @param array   $parameters Values for message template populating.
     * @param integer $code       Optionally override Exception status code.
     **/
    public function __construct(array $parameters = [], int $code = null)
    {
        $this->code       = ($code ?? $this->code);
        $this->parameters = $parameters;

        $this->formatMessage();

        parent::__construct($this->message, $this->code);
    }

    /**
     * Format message with parameters
     *
     * @return void
     **/
    private function formatMessage()
    {
        foreach ($this->parameters as $parameter => $value) {
            $regex         = sprintf('/{%s}/', $parameter);
            $this->message = preg_replace($regex, $value, $this->message);
        }
    }

    /**
     * Getter for template parameters. Allow for exception chaining
     *
     * @return array
     **/
    public function getParameters() : array
    {
        return $this->parameters;
    }
}

<?php

namespace AdrienBrault\StatsDCollector;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Stat
{
    /**
     * @var int|float
     */
    private $value;

    /**
     * @var string One of the Liuggio\StatsdClient\Entity\StatsdDataInterface STATSD_METRIC_* constants
     */
    private $type;

    /**
     * @var array
     */
    private $parameters;

    public function __construct($type, $value, array $parameters = array())
    {
        $this->type = $type;
        $this->value = $value;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}

<?php

namespace AdrienBrault\StatsDCollector;

use Liuggio\StatsdClient\Entity\StatsdData;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class StatsDDataCollector
{
    /**
     * @var CollectorInterface[]
     */
    private $collectors;

    /**
     * @var ParameterProviderInterface[]
     */
    private $parameterProviders;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        array $collectors = array(),
        array $parameterProviders = array(),
        array $parameters = array()
    ) {
        $this->collectors = $collectors;
        $this->parameterProviders = $parameterProviders;
        $this->parameters = $parameters;

        foreach ($this->collectors as $template => $collector) {
            if (!is_string($template)) {
                throw new \InvalidArgumentException(
                    sprintf('The template should be a string, %s given ("%s").', gettype($template), $template)
                );
            }
            if (!$collector instanceof CollectorInterface) {
                throw new \InvalidArgumentException(
                    sprintf('The collector should implement the DataCollectorInterface.')
                );
            }
        }
        foreach ($this->parameterProviders as $parameterProvider) {
            if (!$parameterProvider instanceof ParameterProviderInterface) {
                throw new \InvalidArgumentException(
                    sprintf('The parameter provider should implement the ParameterProviderInterface.')
                );
            }
        }
    }

    /**
     * @return StatsdDataInterface[]
     */
    public function collect()
    {
        $statsData = array();

        $parameters = $this->parameters;
        foreach ($this->parameterProviders as $parameterProvider) {
            $parameters = array_merge($parameters, $parameterProvider->getParameters());
        }

        foreach ($this->collectors as $template => $collector) {
            foreach ($collector->getStats() as $stat) {
                $statsData[] = $this->createStatData($stat, $template, $parameters);
            }
        }

        return $statsData;
    }

    private function createStatData(Stat $stat, $template, array $parameters)
    {
        $parameters = array_merge($parameters, $stat->getParameters());
        $key = preg_replace_callback('/{(?P<name>[^}]+)}/', function ($matches) use ($parameters) {
            $name = $matches['name'];
            if (!array_key_exists($name, $parameters)) {
                throw new \RuntimeException(
                    sprintf('Unknown template parameter "%s", only %s available.', $name, join(', ', array_keys($parameters)))
                );
            }

            return $parameters[$name];
        }, $template);

        $statData = new StatsdData();
        $statData->setMetric($stat->getType());
        $statData->setValue($stat->getValue());
        $statData->setKey($key);

        return $statData;
    }
}

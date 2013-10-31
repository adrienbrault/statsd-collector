<?php

namespace AdrienBrault\StatsDCollector\Collector;

use AdrienBrault\StatsDCollector\Stat;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ExceptionCollector extends AbstractCollector
{
    public function collectException(\Exception $e)
    {
        $this->addStat(
            new Stat(
                StatsdDataInterface::STATSD_METRIC_COUNT,
                1,
                array(
                    'exception_class' => preg_replace(array('/[\\\\]/', '/[^a-z0-9_]/i'), array('_', ''), get_class($e)),
                )
            )
        );
    }
}

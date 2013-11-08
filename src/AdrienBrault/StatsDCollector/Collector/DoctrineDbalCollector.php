<?php

namespace AdrienBrault\StatsDCollector\Collector;

use AdrienBrault\StatsDCollector\Stat;
use Doctrine\DBAL\Logging\SQLLogger;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class DoctrineDbalCollector extends AbstractCollector implements SQLLogger
{
    /**
     * REGEX FTW! Should work for most cases
     */
    const REGEX_TABLE = '
        /^
            (?:
                (?:insert|replace)\s+(?:into )?
                |delete\s+from
                |update
                |select.+from
                |call
            )

            \s+

            (?P<table>
                `?[a-z_0-9]+`?
            )
        /ix'
    ;

    /**
     * @var array
     */
    private $currentQueryParameters;

    /**
     * @var float
     */
    private $currentQueryStartTime;

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $queryType = null;
        if (preg_match('/^(\w+)(?:\s+|$)/', $sql, $matches)) {
            $queryType = strtolower($matches[1]);
        }

        $table = null;
        if (preg_match(self::REGEX_TABLE, $sql, $matches)) {
            $table = strtolower($matches['table']);
        }

        $this->currentQueryStartTime = TimeUtil::getCurrentTime();
        $this->currentQueryParameters = array(
            'query_type' => $queryType,
            'query_table' => $table,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        $duration = TimeUtil::getElapsedTime($this->currentQueryStartTime);

        $this->addStat(
            new Stat(
                StatsdDataInterface::STATSD_METRIC_TIMING,
                $duration,
                $this->currentQueryParameters
            )
        );
    }
}

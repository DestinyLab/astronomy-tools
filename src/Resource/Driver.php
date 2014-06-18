<?php

namespace DestinyLab\AstronomyTools\Resource;

use DestinyLab\Swetest;

abstract class Driver
{
    const UNIT_HOUR = 'hour';
    const UNIT_MINUTE = 'minute';
    const UNIT_SECOND = 'second';

    /** @var \DestinyLab\Swetest */
    protected $swetest;
    protected $baseQuery = [];

    public function __construct(Swetest $swetest)
    {
        $this->swetest = $swetest;
    }

    abstract public function calculate($year);

    protected function dataProcess($data)
    {
        array_walk(
            $data,
            function (&$val) {
                if (preg_match('/(.*)\sUT\s+(.*)$/', $val, $match)) {
                    $val = [
                        'date'   => $match[1],
                        'degree' => $match[2],
                    ];
                }
            }
        );

        return $data;
    }

    abstract protected function computeAngle($data, $type = null);

    abstract protected function locateUnit($data, $unit);

    protected function getUnitQuery($unit)
    {
        $query = null;
        switch ($unit) {
            case static::UNIT_HOUR:
                $query = [
                    'n' => 25,
                    's' => 0.0416666667,
                ];
                break;
            case static::UNIT_MINUTE:
                $query = [
                    'n' => 61,
                    's' => 0.000694444,
                ];
                break;
            case static::UNIT_SECOND:
                $query = [
                    'n' => 61,
                    's' => 0.000011574,
                ];
                break;
        }

        return $query;
    }
}

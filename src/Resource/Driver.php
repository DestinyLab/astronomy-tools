<?php

namespace DestinyLab\AstronomyTools\Resource;

use DestinyLab\Swetest;

abstract class Driver
{
    const ALL = 'all';

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

    public function calculate($year)
    {
        if (! is_int($year)) {
            throw new \InvalidArgumentException("[{$year}] is Invalid!");
        }

        $query = [
            'b' => "1.1.{$year}",
            'ut' => '00:00:00',
            'n' => 366,
        ];
        $query = array_merge($this->baseQuery, $query);
        $this->swetest->query($query)->execute();
        $out = $this->swetest->getOutput();
        $out = $this->computeAngle($this->dataProcess($out), static::ALL);

        $locateHour = $this->locateUnit($out, static::UNIT_HOUR);
        $locateMinute = $this->locateUnit($locateHour, static::UNIT_MINUTE);
        $locateSecond = $this->locateUnit($locateMinute, static::UNIT_SECOND);

        $ret = [];
        foreach ($locateSecond as $type => $arr) {
            foreach ($arr as $v) {
                $dateTime = \DateTime::createFromFormat('d.m.Y H:i:s', $v['date'], new \DateTimeZone('UTC'));
                $ret['t_'.$dateTime->getTimestamp()] = $type;
            }
        }

        ksort($ret);

        return $ret;
    }

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

    protected function locateUnit($data, $unit)
    {
        $ret = [];
        foreach ($data as $type => $arr) {
            foreach ($arr as $k => $v) {
                preg_match('/(.*)\s(.*)/', $v['date'], $m);
                $query = [
                    'b' => $m[1],
                    'ut' => $m[2],
                ];
                $query = array_merge($this->baseQuery, $query, $this->getUnitQuery($unit));
                $this->swetest->query($query)->execute();
                $tmp = $this->computeAngle($this->dataProcess($this->swetest->getOutput()), $type);
                $ret[$type][$k] = $tmp[$type][0];
            }
        }

        return $ret;
    }

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

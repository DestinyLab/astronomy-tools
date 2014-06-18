<?php

namespace DestinyLab\AstronomyTools\Resource;

use DestinyLab\Swetest;
use DestinyLab\AstronomyTools\IPlanets;

class LunarPhases extends Driver
{
    const ALL = 'all';
    const NEW_MOON = 'new';
    const FIRST_QUARTER = 'fisrt_quarter';
    const FULL_MOON = 'full';
    const LAST_QUARTER = 'last_quarter';

    protected $baseQuery;

    public function __construct(Swetest $swetest)
    {
        parent::__construct($swetest);
        $this->baseQuery = [
            'p' => IPlanets::SUN,
            'd' => IPlanets::MOON,
            'f' => 'Tl',
            'eswe',
            'head',
        ];
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

    protected function computeAngle($data, $type = null)
    {
        $ret = [];
        foreach ($data as $k => $v) {
            // 滿月
            in_array($type, [static::FULL_MOON, static::ALL]) and $this->angleFullMoon($ret, $data, $k, $v);

            // 上弦月
            in_array($type, [static::FIRST_QUARTER, static::ALL]) and $this->angleFirstQuarter($ret, $data, $k, $v);

            // 下弦月
            in_array($type, [static::LAST_QUARTER, static::ALL]) and $this->angleLastQuarter($ret, $data, $k, $v);

            // 日月交朔
            in_array($type, [static::NEW_MOON, static::ALL]) and $this->angleNewMoon($ret, $data, $k, $v);
        }

        return $ret;
    }

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

    /**
     * @param $ret
     * @param $data
     * @param $key
     * @param $value
     */
    protected function angleFullMoon(&$ret, $data, $key, $value)
    {
        if ($value['degree'] >= 0 and $key - 1 >= 0 and $data[$key - 1]['degree'] <= 0) {
            $ret[static::FULL_MOON][] = $data[$key - 1];
        }
    }

    /**
     * @param $ret
     * @param $data
     * @param $key
     * @param $value
     */
    protected function angleFirstQuarter(&$ret, $data, $key, $value)
    {
        if ($value['degree'] <= - 90 and $key - 1 >= 0 and $data[$key - 1]['degree'] >= - 90) {
            $ret[static::FIRST_QUARTER][] = $data[$key - 1];
        }
    }

    /**
     * @param $ret
     * @param $data
     * @param $key
     * @param $value
     */
    protected function angleLastQuarter(&$ret, $data, $key, $value)
    {
        if ($value['degree'] <= 90 and $key - 1 >= 0 and $data[$key - 1]['degree'] >= 90) {
            $ret[static::LAST_QUARTER][] = $data[$key - 1];
        }
    }

    /**
     * @param $ret
     * @param $data
     * @param $key
     * @param $value
     */
    protected function angleNewMoon(&$ret, $data, $key, $value)
    {
        if ($value['degree'] <= 0 and $key - 1 >= 0 and $data[$key - 1]['degree'] >= 0) {
            $ret[static::NEW_MOON][] = $data[$key - 1];
        }
    }
}

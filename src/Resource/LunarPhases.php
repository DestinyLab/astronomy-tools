<?php

namespace DestinyLab\AstronomyTools\Resource;

use DestinyLab\Swetest;
use DestinyLab\AstronomyTools\IPlanets;

class LunarPhases extends Driver
{
    const NEW_MOON = 'new';
    const FIRST_QUARTER = 'fisrt_quarter';
    const FULL_MOON = 'full';
    const LAST_QUARTER = 'last_quarter';

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

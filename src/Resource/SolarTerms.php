<?php

namespace DestinyLab\AstronomyTools\Resource;

use DestinyLab\Swetest;
use DestinyLab\AstronomyTools\IPlanets;
use InvalidArgumentException;

class SolarTerms extends Driver
{
    /** 立春 */
    const START_OF_SPRING = 'start_of_spring';

    /** 雨水 */
    const RAIN_WATER = 'rain_water';

    /** 驚蟄 */
    const AWAKENING_OF_INSECTS = 'awakening_of_insects';

    /** 春分 */
    const VERNAL_EQUINOX = 'vernal_equinox';

    /** 清明 */
    const CLEAR_AND_BRIGHT = 'clear_and_bright';

    /** 穀雨 */
    const GRAIN_RAIN = 'grain_rain';

    /** 立夏 */
    const START_OF_SUMMER = 'start_of_summer';

    /** 小滿 */
    const GRAIN_FULL = 'grain_full';

    /** 芒種 */
    const GRAIN_IN_EAR = 'grain_in_ear';

    /** 夏至 */
    const SUMMER_SOLSTICE = 'summer_solstice';

    /** 小暑 */
    const MINOR_HEAT = 'minor_heat';

    /** 大暑 */
    const MAJOR_HEAT = 'major_heat';

    /** 立秋 */
    const START_OF_AUTUMN = 'start_of_autumn';

    /** 處暑 */
    const LIMIT_OF_HEAT = 'limit_of_heat';

    /** 白露 */
    const WHITE_DEW = 'white_dew';

    /** 秋分 */
    const AUTUMNAL_EQUINOX = 'autumnal_equinox';

    /** 寒露 */
    const COLD_DEW = 'cold_dew';

    /** 霜降 */
    const FROST_DESCENT = 'frost_descent';

    /** 立冬 */
    const START_OF_WINTER = 'start_of_winter';

    /** 小雪 */
    const MINOR_SNOW = 'minor_snow';

    /** 大雪 */
    const MAJOR_SNOW = 'major_snow';

    /** 冬至 */
    const WINTER_SOLSTICE = 'winter_solstice';

    /** 小寒 */
    const MINOR_COLD = 'minor_cold';

    /** 大寒 */
    const MAJOR_COLD = 'major_cold';

    protected $solarTerms = [
        self::START_OF_SPRING => 315,
        self::RAIN_WATER => 330,
        self::AWAKENING_OF_INSECTS => 345,
        self::VERNAL_EQUINOX => 0,
        self::CLEAR_AND_BRIGHT => 15,
        self::GRAIN_RAIN => 30,
        self::START_OF_SUMMER => 45,
        self::GRAIN_FULL => 60,
        self::GRAIN_IN_EAR => 75,
        self::SUMMER_SOLSTICE => 90,
        self::MINOR_HEAT => 105,
        self::MAJOR_HEAT => 120,
        self::START_OF_AUTUMN => 135,
        self::LIMIT_OF_HEAT => 150,
        self::WHITE_DEW => 165,
        self::AUTUMNAL_EQUINOX => 180,
        self::COLD_DEW => 195,
        self::FROST_DESCENT => 210,
        self::START_OF_WINTER => 225,
        self::MINOR_SNOW => 240,
        self::MAJOR_SNOW => 255,
        self::WINTER_SOLSTICE => 270,
        self::MINOR_COLD => 285,
        self::MAJOR_COLD => 300,
    ];

    public function __construct(Swetest $swetest)
    {
        parent::__construct($swetest);
        $this->baseQuery = [
            'p' => IPlanets::SUN,
            'f' => 'Tl',
            'eswe',
            'head',
        ];
    }

    protected function computeAngle($data, $type = null)
    {
        $ret = [];
        foreach ($data as $k => $v) {
            foreach (array_keys($this->solarTerms) as $solarTerm) {
                in_array($type, [$solarTerm, static::ALL]) and $this->angle($ret, $data, $solarTerm, $k, $v);
            }
        }

        return $ret;
    }

    /**
     * @param $ret
     * @param $data
     * @param $solarTerm
     * @param $key
     * @param $value
     */
    protected function angle(&$ret, $data, $solarTerm, $key, $value)
    {
        $angle = $this->parseAngle($solarTerm);
        if ($angle === 0) {
            if ($value['degree'] >= $angle + 15 and $key - 1 >= $angle and $data[$key - 1]['degree'] >= 360 - 15) {
                $ret[$solarTerm][0] = $data[$key - 1];
            }
        } else {
            if ($value['degree'] >= $angle and $key - 1 >= 0 and $data[$key - 1]['degree'] <= $angle) {
                $ret[$solarTerm][] = $data[$key - 1];
            }
        }
    }

    protected function parseAngle($solarTerm)
    {
        if (isset($this->solarTerms[$solarTerm])) {
            return $this->solarTerms[$solarTerm];
        }

        throw new InvalidArgumentException('Invalid Solar Term!');
    }
}

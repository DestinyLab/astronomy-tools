<?php

/**
 * This file is part of DestinyLab.
 */

namespace DestinyLab\AstronomyTools;

use DateTime;
use DestinyLab\Swetest;

/**
 * Planets
 *
 * @package DestinyLab\AstronomyTools
 * @author Lance He <indigofeather@gmail.com>
 */
class Planets
{
    /**
     * @var \DestinyLab\Swetest
     */
    protected $swetest;

    /**
     * @var array
     */
    protected $defaultPlanets = [
        IPlanets::SUN,
        IPlanets::MOON,
        IPlanets::MERCURY,
        IPlanets::VENUS,
        IPlanets::MARS,
        IPlanets::JUPITER,
        IPlanets::SATURN,
        IPlanets::URANUS,
        IPlanets::NEPTUNE,
        IPlanets::PLUTO,
    ];

    /**
     * @param Swetest $swetest
     */
    public function __construct(Swetest $swetest)
    {
        $this->swetest = $swetest;
    }

    /**
     * @return Swetest
     */
    public function getSwetest()
    {
        return $this->swetest;
    }

    /**
     * @param DateTime $dateTime
     * @param array    $planets
     * @return array
     * @throws \DestinyLab\SwetestException
     */
    public function longitudeSign(DateTime $dateTime, array $planets = [])
    {
        ! $planets and $planets = $this->defaultPlanets;

        $query = [
            'p' => implode('', $planets),
            'b' => $dateTime->format('d.m.Y'),
            'ut' => $dateTime->format('H:i:s'),
            'f' => 'PZ',
            'eswe',
            'head',
        ];

        $this->swetest->query($query)->execute();

        $data = [];

        foreach ($this->swetest->getOutput() as $item) {
            preg_match('/^(\w+)[\s|\d]+(\w+)/', $item, $matches)
            and $data[strtolower($matches[1])] = $matches[2];
        }

        return $data;
    }
}

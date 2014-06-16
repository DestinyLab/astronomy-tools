<?php

namespace DestinyLab\AstronomyTools;

interface IPlanets
{
    const SUN = '0';
    const MOON = '1';
    const MERCURY = '2';
    const VENUS = '3';
    const MARS = '4';
    const JUPITER = '5';
    const SATURN = '6';
    const URANUS = '7';
    const NEPTUNE = '8';
    const PLUTO = '9';
    const MEAN_LUNAR_NODE = 'm';
    const TRUE_LUNAR_NODE = 't';
    const NUTATION = 'n';
    const OBLIQUITY_OF_ECLIPTIC = 'o';
    const DELTA_T = 'q';
    const TIME_EQUATION = 'y';
    const MEAN_LUNAR_APOGEE = 'A';
    const LILITH = 'A';
    const BLACK_MOON = 'A';
    const OSCULATING_LUNAR_APOGEE = 'B';
    const LUNAR_APOGEE = 'c';
    const LUNAR_PERIGEE = 'g';
    const EARTH = 'C';
}

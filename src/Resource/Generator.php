<?php

namespace DestinyLab\AstronomyTools\Resource;

use DestinyLab\Swetest;
use Fuel\FileSystem\Directory;
use Fuel\FileSystem\File;
use InvalidArgumentException;

class Generator
{
    /**
     * @var \DestinyLab\Swetest
     */
    protected $swetest;
    protected $outputPath = null;

    public function __construct(Swetest $swetest, $outputPath)
    {
        $this->swetest = $swetest;
        $dir = new Directory($outputPath);
        if (! $dir->exists()) {
            throw new InvalidArgumentException('Invalid Path!');
        }

        $this->outputPath = (string) $outputPath;
    }

    public function lunarPhase(array $range, $force = false)
    {
        $lunarPhases = new LunarPhases($this->swetest);
        foreach ($range as $year) {
            $file = new File($this->outputPath.$year.'.json');
            if (! $force and $file->exists()) {
                continue;
            }

            $data = $lunarPhases->calculate($year);
            $file->update(json_encode($data));
            echo $year.' ';
        }
    }

    public function solarTerms(array $range, $force = false)
    {
        $solarTerms = new SolarTerms($this->swetest);
        foreach ($range as $year) {
            $file = new File($this->outputPath.$year.'.json');
            if (! $force and $file->exists()) {
                continue;
            }

            $data = $solarTerms->calculate($year);
            $file->update(json_encode($data));
            echo $year.' ';
        }
    }
}

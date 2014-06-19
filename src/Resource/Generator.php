<?php

namespace DestinyLab\AstronomyTools\Resource;

use DestinyLab\Swetest;
use Fuel\FileSystem\Directory;
use Fuel\FileSystem\File;
use InvalidArgumentException;
use BadMethodCallException;

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

    public function __call($name, $arguments)
    {
        $class = __NAMESPACE__.'\\'.ucfirst($name);
        if (! class_exists($class)) {
            throw new BadMethodCallException('Invalid Method!');
        }

        return $this->generate($class, $arguments[0], $arguments[1]);
    }

    protected function generate($class, array $range, $force = false)
    {
        $object = new $class($this->swetest);
        foreach ($range as $year) {
            $file = new File($this->outputPath.$year.'.json');
            if (! $force and $file->exists()) {
                continue;
            }

            $data = $object->calculate($year);
            $file->update(json_encode($data));
            echo $year.' ';
        }

        return true;
    }
}

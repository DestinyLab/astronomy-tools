<?php

namespace DestinyLab\AstronomyTools;

use Fuel\FileSystem\Directory;
use Fuel\FileSystem\File;
use InvalidArgumentException;

trait GetterTrait
{
    protected $resourcePath;
    protected $years = [];

    public function __construct($resourcePath)
    {
        $dir = new Directory($resourcePath);
        if (! $dir->exists()) {
            throw new InvalidArgumentException('Invalid Path!');
        }

        $this->resourcePath = $resourcePath;
    }

    public function get(array $years)
    {
        $data = [];
        foreach ($years as $year) {
            if (! isset($this->years[$year])) {
                $this->years[$year] = $this->getFileContent($year);
            }

            $data[$year] = $this->years[$year];
        }

        return $data;
    }

    protected function getFileContent($year)
    {
        $file = new File($this->resourcePath.$year.'.json');
        if (! $file->exists()) {
            throw new InvalidArgumentException('File is Not Exist!');
        }

        return json_decode($file->getContents(), true);
    }
}

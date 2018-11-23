<?php

namespace App\Artifactory;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * Class Builder
 *
 * @package App\Artifactory
 */
class Builder
{
    /**
     * @var ZipArchive
     */
    protected $zip;

    /**
     * @var string
     */
    protected $command = 'npm run build';

    /**
     * @var string
     */
    protected $dist = 'dist';

    /**
     * Builder constructor.
     *
     * @param ZipArchive $zip
     */
    public function __construct(ZipArchive $zip)
    {
        $this->zip = $zip;
    }

    /**
     * Builds and archives provided $source directory into $target
     *
     * @param string $source
     * @param string $target
     *
     * @return bool
     */
    public function build(string $source, string $target): bool
    {
        $process = new Process($this->command, $source);

        $result = $process->run();

        if ($result !== 0) {
            return false;
        }

        $rootPath = realpath(\dirname($target));

        $zip = clone $this->zip;

        $zip->open($target, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();
                $relativePath = substr($filePath, \strlen($rootPath) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return true;
    }
}
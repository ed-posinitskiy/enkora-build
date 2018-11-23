<?php

namespace App\Artifactory;

use League\Flysystem\FilesystemInterface;

/**
 * Class Manager
 *
 * @package App\Artifactory
 */
class Manager
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $directory;

    /**
     * Manager constructor.
     *
     * @param FilesystemInterface $filesystem
     * @param string              $directory
     */
    public function __construct(FilesystemInterface $filesystem, string $directory = 'angular')
    {
        $this->filesystem = $filesystem;
        $this->directory  = $directory;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->filesystem->has($this->archive($name));
    }

    /**
     * @param string $name
     * @param string $archivePath
     */
    public function store(string $name, string $archivePath): void
    {
        $this->filesystem->putStream($this->archive($name), $archivePath);
    }

    /**
     * @param string $name
     * @param string $version
     */
    public function bind(string $name, string $version): void
    {
        $this->filesystem->put($this->archive($version), $name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function archive(string $name): string
    {
        return sprintf('%s/%s.zip', $this->directory, $name);
    }

    /**
     * @param string $version
     *
     * @return string
     */
    protected function version(string $version): string
    {
        return sprintf('%s/versions/%s', $this->directory, $version);
    }
}
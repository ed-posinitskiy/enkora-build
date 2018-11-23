<?php

namespace App\Filesystem\Git;

use App\Filesystem\Exception\UnreachableException;
use App\Filesystem\RepositoryInterface;
use GitWrapper\GitCommand;
use GitWrapper\GitProcess;
use GitWrapper\GitWrapper;

/**
 * Class Repository
 *
 * @package App\Filesystem
 */
class Repository implements RepositoryInterface
{
    /**
     * @var GitWrapper
     */
    protected $git;

    /**
     * @var array
     */
    protected $exclude = [
        'node_modules',
    ];

    /**
     * Repository constructor.
     *
     * @param GitWrapper $git
     */
    public function __construct(GitWrapper $git)
    {
        $this->git = $git;
    }

    /**
     * @param string $source
     *
     * @return string
     * @throws UnreachableException
     */
    public function version(string $source): string
    {
        if (!$this->isGit($source)) {
            throw UnreachableException::withSource($source);
        }

        return $this->git->git('rev-parse HEAD', $source);
    }

    /**
     * @param string $source
     *
     * @return string
     * @throws UnreachableException
     */
    public function contentHash(string $source): string
    {
        if (!$this->isGit($source)) {
            throw UnreachableException::withSource($source);
        }

        $iterator    = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source));
        $contentHash = [];

        /** @var \SplFileInfo $item */
        foreach ($iterator as $item) {
            if (!$item->isFile()) {
                continue;
            }

            if ($this->shouldBeExcluded($item)) {
                continue;
            }

            $filePath                                     = $item->getRealPath();
            $contentHash[$this->normalizeName($filePath)] = hash('md4', @file_get_contents($filePath));
        }

        ksort($contentHash);

        return md5(implode('', $contentHash));
    }

    /**
     * @param string $source
     *
     * @return bool
     */
    protected function isGit(string $source): bool
    {
        $command = new GitCommand('rev-parse --git-dir');
        $command->executeRaw(true);
        $command->setDirectory($source);

        $process = new GitProcess($this->git, $command);

        return $process->run() === 0;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    protected function normalizeName(string $input): string
    {
        return preg_replace('/\W/', '', $input);
    }

    /**
     * @param \SplFileInfo $item
     *
     * @return bool
     */
    protected function shouldBeExcluded(\SplFileInfo $item): bool
    {
        foreach ($this->exclude as $prefix) {
            // @TODO: Implementation is broken, MUST be updated to the relative one
            if (0 === stripos(trim($item->getPath(), '/'), $prefix)) {
                return true;
            }
        }

        return false;
    }
}
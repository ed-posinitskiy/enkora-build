<?php

namespace App\Filesystem;

use App\Filesystem\Exception\UnreachableException;

/**
 * Interface RepositoryInterface
 *
 * @package App\Filesystem
 */
interface RepositoryInterface
{
    /**
     * @param string $source
     *
     * @return string
     * @throws UnreachableException
     */
    public function version(string $source): string;

    /**
     * @param string $source
     *
     * @return string
     * @throws UnreachableException
     */
    public function contentHash(string $source): string;
}
<?php

namespace App\Filesystem\Exception;

use Exception;

/**
 * Class UnreachableException
 *
 * @package App\Filesystem\Exception
 */
class UnreachableException extends Exception
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @return string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @param string $message
     *
     * @return UnreachableException
     */
    public static function withSource(string $source, ?string $message = null): UnreachableException
    {
        $message           = $message ?? sprintf('Source directory [%s] is unreachable');
        $exception         = new static($message);
        $exception->source = $source;

        return $exception;
    }
}
<?php

namespace App\Provider;

use Illuminate\Contracts\Container\Container;

/**
 * Interface ProviderInterface
 *
 * @package App\Provider
 */
interface ProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void;

    /**
     * @param Container $container
     */
    public function boot(Container $container): void;
}
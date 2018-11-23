<?php

namespace App\Provider;

use App\Filesystem\Git\Repository;
use App\Filesystem\RepositoryInterface;
use Aws\S3\S3Client;
use GitWrapper\GitWrapper;
use Illuminate\Contracts\Container\Container;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

/**
 * Class AppServiceProvider
 *
 * @package App\Provider
 */
class AppServiceProvider implements ProviderInterface
{

    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container->singleton(FilesystemInterface::class, function (Container $container) {
            $config = $container->get('config')['aws'] ?? [];
            $client = new S3Client(
                [
                    'credentials' => [
                        'key'    => $config['key'] ?? '',
                        'secret' => $config['secret'] ?? '',
                    ],
                    'region'      => $config['region'] ?? '',
                    'version'     => 'latest',
                ]
            );

            return new Filesystem(new AwsS3Adapter($client, $config['bucket'] ?? ''));
        });

        $container->singleton(RepositoryInterface::class, function (Container $container) {
            return new Repository(new GitWrapper());
        });
    }

    /**
     * @param Container $container
     */
    public function boot(Container $container): void
    {
        // TODO: Implement boot() method.
    }
}
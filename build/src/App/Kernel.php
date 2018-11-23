<?php

namespace App;

use App\Provider\ProviderInterface;
use Illuminate\Container\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

/**
 * Class Kernel
 *
 * @package App
 */
class Kernel
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var ProviderInterface[]
     */
    protected $providers = [];

    /**
     * Kernel constructor.
     *
     * @param string $configPath
     */
    public function __construct(string $configPath)
    {
        $this->container = Container::getInstance();

        $this->loadConfiguration($configPath);
    }

    /**
     * @return Application
     */
    public function bootstrap(): Application
    {
        if ($this->application) {
            return $this->application;
        }

        $config    = $this->container->get('config');
        $providers = $config['providers'] ?? [];

        $this->application = new Application(
            $config['app.name'] ?? 'Enkora build tool',
            $config['app.version'] ?? '0.1-alpha'
        );

        $this->container->instance(Application::class, $this->application);

        foreach ($providers as $providerFQCN) {
            $this->register($providerFQCN);
        }

        foreach ($this->providers as $provider) {
            $provider->boot($this->container);
        }

        foreach ($config['commands'] ?? [] as $commandFQCN) {
            $command = $this->container->make($commandFQCN);

            if (!$command instanceof Command) {
                throw new \RuntimeException(
                    sprintf('CLI Command [%s] expected to be instance of [%s]', $commandFQCN, Command::class)
                );
            }

            $this->application->add($command);
        }

        return $this->application;
    }

    public function register(string $providerFQCN): void
    {
        $this->providers[] = $provider = new $providerFQCN();

        if (!$provider instanceof ProviderInterface) {
            throw new \RuntimeException(
                sprintf('Provider [%s] expected to be instance of [%s]', $providerFQCN, ProviderInterface::class)
            );
        }

        $provider->register($this->container);
    }

    /**
     * @param string $configPath
     */
    protected function loadConfiguration(string $configPath): void
    {
        if (!(is_readable($configPath) && is_file($configPath))) {
            throw new \RuntimeException(
                sprintf('Configuration file provided [%s] is not readable', $configPath)
            );
        }

        $this->container->instance('config', require $configPath);
    }
}
<?php

namespace App\Command;

use App\Artifactory\Builder;
use App\Artifactory\Manager;
use App\Filesystem\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BuildAngularCommand
 *
 * @package App\Command
 */
class BuildAngularCommand extends Command
{
    /**
     * @var Manager
     */
    protected $artifactory;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * BuildAngularCommand constructor.
     *
     * @param Manager             $artifactory
     * @param RepositoryInterface $repository
     * @param Builder             $builder
     */
    public function __construct(Manager $artifactory, RepositoryInterface $repository, Builder $builder)
    {
        $this->artifactory = $artifactory;
        $this->repository  = $repository;
        $this->builder     = $builder;

        parent::__construct('build:angular');
    }

    /**
     *
     */
    protected function configure()
    {
        $this->addArgument(
            'source',
            InputArgument::REQUIRED,
            'Angular sources directory'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws \App\Filesystem\Exception\UnreachableException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');

        $version = $this->repository->version($source);
        $output->writeln(sprintf('Version: %s', $version));

        $hash = $this->repository->contentHash($source);
        $output->writeln(sprintf('Content hash: %s', $hash));

        return 0;

        if (!$this->build($source, $hash, $output)) {
            return 1;
        }

        $output->writeln(sprintf('Binding artifact [%s] to version [%s]', $hash, $version));
        $this->artifactory->bind($hash, $version);

        return 0;
    }

    /**
     * @param string          $source
     * @param string          $hash
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function build(string $source, string $hash, OutputInterface $output): bool
    {
        $hasArtifact = $this->artifactory->has($hash);

        if ($hasArtifact) {
            $output->writeln('Artifact already exist');

            return true;
        }

        $target = sprintf('%s/%s.zip', sys_get_temp_dir(), $hash);

        $output->writeln('Building artifact ...');

        if (!$this->builder->build($source, $target)) {
            return false;
        }

        $output->writeln('Saving artifact');

        $this->artifactory->store($hash, $target);

        return true;
    }

}
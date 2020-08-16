<?php

namespace App\Console\Commands;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Component\Console\Command\Command;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class FixturesLoadCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private array $paths;

    /**
     * FixturesLoadCommand constructor.
     * @param EntityManagerInterface $em
     * @param array $paths
     */
    public function __construct(EntityManagerInterface $em, array $paths)
    {
        parent::__construct();
        $this->entityManager = $em;
        $this->paths = $paths;
    }

    protected function configure()
    {
        $this->setName('fixtures:load')
            ->setDescription('Load fixtures to DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Loading fixtures...</comment>');

        $loader = new Loader();

        foreach ($this->paths as $path) {
            $loader->loadFromDirectory($path);
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);

        $executor->setLogger(static function ($message) use ($output){
            $output->writeln($message);
        });

        $executor->execute($loader->getFixtures(), true);

        $output->writeln('<info>Fixtures loaded.</info>');

        return 0;
    }

}
<?php

namespace Lwz\LaravelExtend\Artisan\Make\Seeds;

use Illuminate\Database\Console\Seeds\SeedCommand as Command;
use Lwz\LaravelExtend\Artisan\Make\CommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedCommand extends Command
{
    use CommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ext-db:seed';


    public function configure()
    {
        $this->addArgument('service', InputArgument::REQUIRED, 'service name');
    }

    /**
     * Get a seeder instance from the container.
     *
     * @return \Illuminate\Database\Seeder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getSeeder()
    {
        $class = $this->input->getArgument('class') ?? ($this->input->getOption('class') ?? $this->getDefaultSeedClass());

        if (strpos($class, '\\') === false) {
            $class = $this->getSeedClassPrefix() . $class;
        }

        if ($class === $this->getDefaultSeedClass() &&
            !class_exists($class)) {
            $class = 'DatabaseSeeder';
        }

        return $this->laravel->make($class)
            ->setContainer($this->laravel)
            ->setCommand($this);
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
        ];
    }

    private function getDefaultSeedClass(): string
    {
        return $this->getSeedClassPrefix() . 'DatabaseSeeder';
    }

    private function getSeedClassPrefix(): string
    {
        return $this->getNamespacePrefix() . 'Database\\Seeders\\';
    }
}

<?php

namespace NascentAfrica\EloquentRepository\Console;


use Symfony\Component\Console\Input\InputOption;

/**
 * Class InterfaceMakeCommand
 *
 * @package NascentAfrica\EloquentRepository\Console
 * @author Anitche Chisom
 */
class InterfaceMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'na:interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new eloquent repository interface class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository Interface';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/interface.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('nascent-africa.repository.generator.namespaces.interfaces', '\Contracts\Repositories');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
        ];
    }
}

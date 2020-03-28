<?php

namespace NascentAfrica\EloquentRepository\Console;


/**
 * Class ProviderMakeCommand
 *
 * @package NascentAfrica\EloquentRepository\Console
 * @author Anitche Chisom
 */
class ProviderMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'na:provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository service provider.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Provider';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/provider.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('nascent-africa.repository.generator.namespaces.providers', '\Providers');
    }
}

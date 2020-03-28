<?php

namespace NascentAfrica\EloquentRepository\Console;


use Symfony\Component\Console\Input\InputOption;

/**
 * Class CriteriaMakeCommand
 *
 * @package NascentAfrica\EloquentRepository\Console
 * @author Anitche Chisom
 */
class CriteriaMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'na:criteria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Criteria';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/criteria.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('nascent-africa.repository.generator.namespaces.criteria', '\Criteria');
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

<?php

namespace NascentAfrica\EloquentRepository\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class BindingCommand
 *
 * @package NascentAfrica\EloquentRepository\Console
 * @author Anitche Chisom
 */
class BindingCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'na:bind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bind repository with interface.';

    /**
     * The placeholder for repository bindings
     *
     * @var string
     */
    public $bindPlaceholder = '//:end-bindings:';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Add entity repository binding to the repository service provider
        $providerPath = $this->getProvider();

        $provider = File::get($providerPath);

        $repositoryInterface = '\\' . $this->option('interface') . "::class";

        $repositoryEloquent = '\\' . $this->option('concrete') . "::class";

        File::put($providerPath, str_replace($this->bindPlaceholder,
            "\$this->app->bind({$repositoryInterface}, {$repositoryEloquent});"
            . PHP_EOL . '        ' . $this->bindPlaceholder, $provider));

        $this->info('Binding was successful.');
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        $provider =  config('generator.basePath', $this->laravel['path']).'/'
            . str_replace('\\', '/', config('nascent-africa.repository.namespaces.providers', '\Providers'))
            . '/' . config('nascent-africa.repository.provider', 'RepositoryServiceProvider') . '.php';

        if (! file_exists($provider)) {
            if ($this->confirm('A repository service provider does not exist, would you like to create one now?', true)) {
                $this->call('na:provider', [
                    'name' => $this->option('provider')
                ]);
            }
        }

        return $provider;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['provider', 'p', InputOption::VALUE_REQUIRED, 'The service provider where this binding will occur.'],

            ['concrete', 'c', InputOption::VALUE_REQUIRED, 'The concrete class that is to be bound with an interface.'],

            ['interface', 'i', InputOption::VALUE_REQUIRED, 'The interface that is to be bound with a concrete class.']
        ];
    }
}

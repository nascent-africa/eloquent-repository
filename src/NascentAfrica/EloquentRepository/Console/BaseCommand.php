<?php

namespace NascentAfrica\EloquentRepository\Console;


use Illuminate\Console\GeneratorCommand as Command;
use Illuminate\Support\Str;

/**
 * Class BaseCommand
 *
 * @package NascentAfrica\EloquentRepository\Console
 * @author Anitche Chisom
 */
abstract class BaseCommand extends Command
{
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return config('nascent-africa.repository.generator.rootNamespace', $this->laravel->getNamespace());
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return config('nascent-africa.repository.generator.basePath', $this->laravel['path']).'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the fully-qualified generatedClass class name.
     *
     * @param  string  $generatedClass
     * @param string $defaultNamespace
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseClass($generatedClass, $defaultNamespace)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $generatedClass)) {
            throw new \InvalidArgumentException('Model name contains invalid characters.');
        }

        $generatedClass = trim(str_replace('/', '\\', $generatedClass), '\\');

        if (! Str::startsWith($generatedClass, $rootNamespace = $this->getFullClassNamespace($defaultNamespace))) {
            $generatedClass = $rootNamespace.'\\'.$generatedClass;
        }

        return $generatedClass;
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseClass($this->option('model'), config('nascent-africa.repository.namespaces.models', ''));

        $this->info(! class_exists($modelClass));

        if (! class_exists($modelClass)) {

            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }

        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Get the full class namespace.
     *
     * @param string $defaultNamespace
     * @return string
     */
    protected function getFullClassNamespace($defaultNamespace)
    {
        return Str::replaceLast('\\', '', $this->rootNamespace()).$defaultNamespace;
    }
}

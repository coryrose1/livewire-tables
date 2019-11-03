<?php

namespace Coryrose\LivewireTables\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Commands\ComponentParser;
use Livewire\Commands\FileManipulationCommand;

class MakeLivewireTableCommand extends FileManipulationCommand
{
    use DetectsApplicationNamespace;

    protected $signature = 'livewire-tables:make {name} {--force}';
    protected $description = 'Create a new Livewire Table component.';

    public function handle()
    {
        $this->parser = new ComponentParser(
            config('livewire.class_namespace', 'App\\Http\\Livewire\\Tables'),
            config('livewire.view_path', resource_path('views/livewire/tables')),
            $this->argument('name')
        );

        $force = $this->option('force');

        $class = $this->createClass($force);
        $view = $this->createView($force);

        $this->refreshComponentAutodiscovery();

        ($class && $view) && $this->line("<options=bold,reverse;fg=green> TABLE COMPONENT CREATED </> 🤙\n");
        $class && $this->line("<options=bold;fg=green>CLASS:</> {$this->parser->relativeClassPath()}");
        $view && $this->line("<options=bold;fg=green>VIEW:</>  {$this->parser->relativeViewPath()}");
    }

    protected function createClass($force = false)
    {
        $classPath = $this->parser->classPath();
        if (File::exists($classPath) && ! $force) {
            $this->line("<options=bold,reverse;fg=red> WHOOPS-IE-TOOTLES </> 😳 \n");
            $this->line("<fg=red;options=bold>Class already exists:</> {$this->parser->relativeClassPath()}");

            return false;
        }
        $this->ensureDirectoryExists($classPath);
        File::put($classPath, $this->classContents());

        return $classPath;
    }

    protected function createView($force = false)
    {
        $viewPath = $this->parser->viewPath();
        if (File::exists($viewPath) && ! $force) {
            $this->line("<fg=red;options=bold>View already exists:</> {$this->parser->relativeViewPath()}");

            return false;
        }
        $this->ensureDirectoryExists($viewPath);
        File::put($viewPath, $this->viewContents());

        return $viewPath;
    }

    public function classContents()
    {
        $template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'component.stub');

        return preg_replace_array(
            ['/\[namespace\]/', '/\[class\]/', '/\[view\]/'],
            [$this->parser->classNamespace(), $this->parser->className(), $this->viewName()],
            $template
        );
    }

    public function viewContents()
    {
        $template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'view.stub');

        return preg_replace(
            '/\[class\]/',
            $this->parser->className(),
            $template
        );
    }

    public function viewName()
    {
        $directories = preg_split('/[.]+/', $this->argument('name'));

        $component = Str::kebab(array_pop($directories));
        $directories = array_map([Str::class, 'studly'], $directories);

        return collect()
            ->push('livewire.tables')
            ->concat($directories)
            ->map([Str::class, 'kebab'])
            ->push($component)
            ->implode('.');
    }
}

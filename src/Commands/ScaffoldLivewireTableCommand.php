<?php

namespace Coryrose\LivewireTables\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\File;
use Livewire\Commands\ComponentParser;
use Livewire\Commands\FileManipulationCommand;
use ReflectionClass;

class ScaffoldLivewireTableCommand extends FileManipulationCommand
{
    use DetectsApplicationNamespace;

    protected $signature = 'livewire-tables:scaffold {name}';
    protected $description = 'Scaffold a Livewire Table component.';

    public function handle()
    {
        $this->parser = new ComponentParser(
            config('livewire.class_namespace', 'App\\Http\\Livewire\\Tables'),
            config('livewire.view_path', resource_path('views/livewire/tables')),
            $this->argument('name')
        );

        $view = $this->createView();

        $view && $this->line('<options=bold,reverse;fg=green> Table Scaffolded </> ');
        $view && $this->line("<options=bold;fg=green>VIEW:</>  {$this->parser->relativeViewPath()}");
    }

    protected function createView()
    {
        $viewPath = $this->parser->viewPath();
        $this->ensureDirectoryExists($viewPath);
        File::put($viewPath, $this->viewContents());

        return $viewPath;
    }

    protected function viewContents()
    {
        $template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'table.stub');
        // get the fields
        $tableComponent = new ReflectionClass($this->parser->baseClassNamespace.'\\'.$this->parser->className());
        $properties = $tableComponent->getDefaultProperties();
        $fields = $properties['fields'];

        return preg_replace(
            '/\[header\]/',
            $this->headerRows($fields),
            preg_replace(
                '/\[data\]/',
                $this->dataRows($fields),
                $template
            )
        );
    }

    protected function headerRows(array $fields)
    {
        $cells = [];
        foreach ($fields as $key => $field) {
            $cell = '<td'.$this->getHeaderClass($field).$this->getSortableAction($key, $field).'>'.$field['title'].'</td>'.PHP_EOL;
            array_push($cells, $cell);
        }

        return implode('', $cells);
    }

    protected function dataRows(array $fields)
    {
        $cells = [];
        foreach ($fields as $key => $field) {
            $cell = '<td'.$this->getDataClass($field).'>{{ $d->'.str_replace('.','->',$field['name']).' }}</td>'.PHP_EOL;
            array_push($cells, $cell);
        }

        return implode('', $cells);
    }

    protected function getDataClass(array $field)
    {
        if (isset($field['cell_class']) && $field['cell_class'] && $field['cell_class'] !== '') {
            return ' class="'.$field['cell_class'].'"';
        }
    }

    protected function getHeaderClass(array $field)
    {
        if (isset($field['header_class']) && $field['header_class'] && $field['header_class'] !== '') {
            return ' class="'.$field['header_class'].'"';
        }
    }

    protected function getSortableAction($key, array $field)
    {
        if (isset($field['sortable']) && $field['sortable']) {
            return ' wire:click="$emit(\'sortColumn\', '.$key.')"';
        }
    }
}

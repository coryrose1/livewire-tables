<?php

namespace Coryrose\LivewireTables;

use Illuminate\Support\Str;
use Livewire\Component;

class LivewireModelTable extends Component
{
    public $sortField = null;

    public $sortDir = null;

    public $search = null;

    protected $listeners = ['sortColumn' => 'setSort'];

    public function setSort($column)
    {
        $this->sortField = array_key_exists('sort_field',
            $this->fields[$column]) ? $this->fields[$column]['sort_field'] : $this->fields[$column]['name'];
        if (! $this->sortDir) {
            $this->sortDir = 'asc';
        } elseif ($this->sortDir == 'asc') {
            $this->sortDir = 'desc';
        } else {
            $this->sortDir = null;
            $this->sortField = null;
        }
    }

    protected function query()
    {
        $model = app($this->model());

        return $this->parseQuery($model, $model->newQuery());
    }

    public function parseQuery($model, $query)
    {
        if ($this->with()) {
            if ($this->sortIsRelatedField()) {
                $query = $this->sortByRelatedField($model, $query);
            } else {
                $query = $this->sort($query->with($this->with()));
            }
        } else {
            $query = $this->sort($query);
        }

        if ($this->hasSearch && $this->search && $this->search !== '') {
            $query = $this->search($query);
        }

        return $this->paginate($query);
    }

    protected function sort($query)
    {
        if (! $this->sortField || ! $this->sortDir) {
            return $query;
        }

        return $query->orderBy($this->sortField, $this->sortDir);
    }

    protected function search($query)
    {
        return $query->whereLike(collect($this->fields)->where('searchable', true)->toArray(), $this->search);
    }

    protected function paginate($query)
    {
        if (! $this->paginate) {
            return $query->get();
        }

        return $query->paginate($this->pagination ?? 15);
    }

    protected function sortByRelatedField($model, $query)
    {
        $relations = collect(explode('.', $this->sortField));
        $relationship = $relations->first();
        $sortField = $relations->pop();

        return $query->with($this->with())
            ->join($model->{$relationship}()->getRelated()->getTable(), $model->getTable().'.'.$model->getKeyName(),
                '=',
                $model->{$relationship}()->getRelated()->getTable().'.'.$model->{$relationship}()->getForeignKeyName())
            ->orderBy($model->{$relationship}()->getRelated()->getTable().'.'.$sortField, $this->sortDir);
    }

    public function model()
    {
        return null;
    }

    protected function with()
    {
        return [];
    }

    /**
     * @return bool
     */
    protected function sortIsRelatedField(): bool
    {
        return $this->sortField && Str::contains($this->sortField, '.') && $this->sortDir;
    }

    public function clearSearch()
    {
        $this->search = null;
    }
}

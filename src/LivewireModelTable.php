<?php

namespace Coryrose\LivewireTables;

use Illuminate\Support\Str;
use Livewire\Component;

class LivewireModelTable extends Component
{
    public $sortField = null;
    public $sortDir = null;

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

        return $this->paginate($query);
    }

    protected function sort($query)
    {
        if (! $this->sortField || ! $this->sortDir) {
            return $query;
        }

        return $query->orderBy($this->sortField, $this->sortDir);
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
        $sortField = $relations->pop();


        if ($relations->count() > 1)
        {
            $relatedSortField = $relations->pop();
            return $query->with([
                $relations->implode(', '),
                $relatedSortField => function ($q) use ($sortField) {
                    $q->orderBy($sortField, $this->sortDir);
                },
            ]);
        } else {
            // theres one relationship and field
            // use with and then join finding correct keys
            // use orderBy on field

            $relationship = $relations->first();

            return $query->with($relationship)
                ->join($model->{$relationship}()->getRelated()->getTable(),
                    $model->{$relationship}()->getRelated()->getTable() . '.' . $model->{$relationship}()->getForeignKeyName(),
                    '=',
                    $model->getTable() . '.' . $model->getKeyName());
        }
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
}

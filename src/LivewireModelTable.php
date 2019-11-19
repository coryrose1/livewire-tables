<?php

namespace Coryrose\LivewireTables;

use Illuminate\Support\Str;
use Livewire\Component;

class LivewireModelTable extends Component
{
    public $sortField = null;
    public $sortDir = null;
    public $search = null;
    public $paginate = true;
    public $pagination = 10;
    public $hasSearch = true;
    public $fields = [];
    public $css;

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
        return $this->paginate($this->buildQuery());
    }

    protected function querySql()
    {
        return $this->buildQuery()->toSql();
    }

    protected function buildQuery()
    {
        $model = app($this->model());
        $query = $model->newQuery();
        $queryFields = $this->generateQueryFields($model);
        if ($this->with()) {
            $query = $this->joinRelated($query, $model);
            if ($this->sortIsRelatedField()) {
                $query = $this->sortByRelatedField($query, $model);
            } else {
                $query = $this->sort($query);
            }
        } else {
            $query = $this->sort($query);
        }
        $query = $this->setSelectFields($query, $queryFields);
        if ($this->hasSearch && $this->search && $this->search !== '') {
            $query = $this->search($query, $queryFields);
        }

        return $query;
    }

    protected function sort($query)
    {
        if (! $this->sortField || ! $this->sortDir) {
            return $query;
        }

        return $query->orderBy($this->sortField, $this->sortDir);
    }

    protected function search($query, $queryFields)
    {
        $searchFields = $queryFields->where('searchable', true)->pluck('name');
        $firstSearch = $searchFields->shift();
        $query = $query->where($firstSearch, 'LIKE', "%{$this->search}%");
        if ($searchFields->count() > 0) {
            foreach ($searchFields->toArray() as $searchField) {
                $query = $query->orWhere($searchField, 'LIKE', "%{$this->search}%");
            }
        }

        return $query;
    }

    protected function paginate($query)
    {
        if (! $this->paginate) {
            return $query->get();
        }

        return $query->paginate($this->pagination ?? 15);
    }

    protected function sortByRelatedField($query, $model)
    {
        $relations = collect(explode('.', $this->sortField));
        $relationship = $relations->first();
        $sortField = $relations->pop();

        return $query->orderBy($model->{$relationship}()->getRelated()->getTable().'.'.$sortField, $this->sortDir);
    }

    public function model()
    {
    }

    protected function with()
    {
        return [];
    }

    public function clearSearch()
    {
        $this->search = null;
    }

    /**
     * @return bool
     */
    protected function sortIsRelatedField(): bool
    {
        return $this->sortField && Str::contains($this->sortField, '.') && $this->sortDir;
    }

    protected function joinRelated($query, $model)
    {
        $query = $query->with($this->with());
        foreach ($this->with() as $relationship) {
            $query = $query->leftJoin($model->{$relationship}()->getRelated()->getTable(),
                $model->getTable().'.'.$model->getKeyName(), '=',
                $model->{$relationship}()->getRelated()->getTable().'.'.$model->{$relationship}()->getForeignKeyName());
        }

        return $query;
    }

    protected function setSelectFields($query, $queryFields)
    {
        return $query->select($queryFields->pluck('name')->toArray());
    }

    protected function generateQueryFields($model)
    {
        return (collect($this->fields))->transform(function ($selectField) use ($model) {
            if ($selectField['name'] == 'id') {
                $selectField['name'] = $model->getTable().'.id';
            } elseif (Str::contains($selectField['name'], '.')) {
                $fieldParts = explode('.', $selectField['name']);
                $selectField['name'] = $model->{$fieldParts[0]}()->getRelated()->getTable().'.'.$fieldParts[1];
            }

            return $selectField;
        });
    }
}

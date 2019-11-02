<?php

namespace Coryrose\LivewireTables;

use Livewire\Component;

class LivewireTable extends Component
{
    public $sortField = null;

    public $sortDir = null;

    protected $listeners = ['sortColumn' => 'setSort'];

    public function setSort($column)
    {
        $this->sortField = array_key_exists('sort_field',
            $this->fields[$column]) ? $this->fields[$column]['sort_field'] : $this->fields[$column]['name'];
    }

    protected function query()
    {
        return $this->filter(app($this->model())->newQuery());
    }

    protected function filter($query)
    {
        $query = $this->sort($query);

        if ($this->paginate) {
            $query = $this->paginate($query);
        } else {
            return $query->get();
        }

        return $query;
    }

    protected function sort($query)
    {
        if (! $this->sortField) {
            return $query;
        }
        if (! $this->sortDir) {
            $this->sortDir = 'asc';
        } elseif ($this->sortDir == 'asc') {
            $this->sortDir = 'desc';
        } else {
            $this->sortDir = null;
            $this->sortField = null;

            return $query;
        }

        return $query->orderBy($this->sortField, $this->sortDir);
    }

    protected function paginate($query)
    {
        if ($this->paginate) {
            return $query->paginate($this->pagination ?? 15);
        }
    }
}

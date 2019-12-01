<?php


namespace Coryrose\LivewireTables;


trait Selectable
{
    public $selected = [];

    public $allRowsSelected = false;

    public function selectAllRows($selected)
    {
        $this->query()->each(function ($row) use($selected) {
            $this->selected[$row->id] = $selected;
        });
    }

    public function selectRow($value, $id)
    {
        if ($value === false) {
            $this->allRowsSelected = false;
        }
    }

    public function clearSelected()
    {
        $this->allRowsSelected = false;

        $this->selected = [];

        $this->query()->each(function ($row) {
            $this->selected[$row->id] = false;
        });
    }

}
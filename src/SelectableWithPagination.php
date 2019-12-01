<?php


namespace Coryrose\LivewireTables;


use Livewire\WithPagination;

trait SelectableWithPagination
{
    use WithPagination, Selectable;

    public function previousPage()
    {
        $this->paginator['page'] = $this->paginator['page'] - 1;

        $this->clearSelected();
    }

    public function nextPage()
    {
        $this->paginator['page'] = $this->paginator['page'] + 1;

        $this->clearSelected();
    }

    public function gotoPage($page)
    {
        $this->paginator['page'] = $page;

        $this->clearSelected();
    }
}
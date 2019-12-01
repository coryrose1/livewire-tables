<?php


namespace Coryrose\LivewireTables;


use Livewire\WithPagination as LivewireWithPagination;

trait WithPagination
{
    use LivewireWithPagination;

    public function previousPage()
    {
        $this->paginator['page'] = $this->paginator['page'] - 1;

        $this->clearSelectable();
    }

    public function nextPage()
    {
        $this->paginator['page'] = $this->paginator['page'] + 1;

        $this->clearSelectable();
    }

    public function gotoPage($page)
    {
        $this->paginator['page'] = $page;

        $this->clearSelectable();
    }

    private function clearSelectable()
    {
        if(method_exists($this, 'clearSelected')){
            $this->clearSelected();
        }
    }
}
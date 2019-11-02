# livewire-tables

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

An extension for [Livewire](https://laravel-livewire.com/docs/quickstart/) that allows you to effortlessly scaffold datatables with optional pagination, search, and sort.

## Installation

Via Composer

``` bash
$ composer require coryrose/livewire-tables
```

## Usage
### Build a table component class
Run the make command to generate a table class:

`php artisan livewire-tables:make UsersTable`

*App/Http/Livewire/Components/Tables/UsersTable.php*

```
...

class UsersTable extends LivewireTable
{
    use WithPagination;

    public $paginate = true;
    public $pagination = 10;

    public $fields = [
        [
            ‘title’ => ‘ID’,
            ‘name’ => ‘id’,
            ‘header_class’ => ‘’,
            ‘cell_class’ => ‘’,
            ‘sortable’ => true,
        ]
    ];

    public function render()
    {
        return view(‘livewire.tables.users-table’, [
            ‘fields’ => $this->fields,
            ‘data’ => $this->query(),
        ]);
    }

    protected function model()
    {
        return User::class;
    }
}
```

Customize the $fields array and model as desired. 

### Scaffold the table view

When ready, scaffold the table view using the scaffold command:

`php artisan livewire-tables:scaffold UsersTable`

*resources/views/livewire/tables/users-table.blade.php*

```
<div>
    <table>
        <thead>
        <tr>
           <td wire:click=“$emit(‘sortColumn’, 0)”>ID</td>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ $d->id }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if ($paginate)
    <div>
        {{ $data->links() }}
    </div>
    @endif
</div>
```

You can use the scaffold command continuously as you make changes to the parent component class.

Since the rendered template is simple HTML, there’s no need for table “slots” for customization - customize the template as you see fit!

## Todo
- Search functionality
- Further support for more advanced queries than a model
- Further configuration values, general table CSS, etc.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Credits

- [Cory Rosenwald][link-author]
- [Laravel Livewire](https://laravel-livewire.com/docs/quickstart/)
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/coryrose/livewire-tables.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/coryrose/livewire-tables.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/coryrose/livewire-tables/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/coryrose/livewire-tables
[link-downloads]: https://packagist.org/packages/coryrose/livewire-tables
[link-travis]: https://travis-ci.org/coryrose/livewire-tables
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/coryrose
[link-contributors]: ../../contributors

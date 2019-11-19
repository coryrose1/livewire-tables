# Livewire-Tables

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

An extension for [Livewire](https://laravel-livewire.com/docs/quickstart/) that allows you to effortlessly scaffold datatables with optional pagination, search, and sort.

Live demo website will be available soon.

## Installation

Via Composer

``` bash
$ composer require coryrose/livewire-tables
```

The package will automatically register its service provider.

To publish the configuration file to `config/livewire-tables.php` run:

```
php artisan vendor:publish --provider="Coryrose\LivewireTables\LivewireTablesServiceProvider"
```

## Usage

Livewire tables are created in three simple steps:
1. [Create a table component class](#create-a-table-component-class)
2. [Configure the table class using the available options](#configure-the-component-options)
3. [Scaffold the table view (as needed when component class changes)](#scaffold-the-table-view)

###  Create a table component class
Run the make command to generate a table class:

`php artisan livewire-tables:make UsersTable`

*App/Http/Livewire/Tables/UsersTable.php*

```
...

class UsersTable extends LivewireModelTable
{
    use WithPagination;
    
        public $paginate = true;
        public $pagination = 10;
        public $hasSearch = true;
    
        public $fields = [
            [
                'title' => 'ID',
                'name' => 'id',
                'header_class' => '',
                'cell_class' => '',
                'sortable' => true,
            ],
            [
                'title' => 'Name',
                'name' => 'name',
                'header_class' => '',
                'cell_class' => '',
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'title' => 'City',
                'name' => 'address.city',
                'header_class' => 'bolded',
                'cell_class' => 'bolded bg-green',
                'sortable' => true,
                'searchable' => true,
            ],
            [
                'title' => 'Post',
                'name' => 'post.content',
                'header_class' => '',
                'cell_class' => '',
                'sortable' => true,
                'searchable' => true,
            ],
        ];
    
        public function render()
        {
            return view('livewire.tables.users-table', [
                'rowData' => $this->query(),
            ]);
        }
    
        public function model()
        {
            return User::class;
        }
    
        public function with()
        {
            return ['address', 'post'];
        }
}
```

### Configure the component options

First, set your base model in the `model()` method in the following format:
```
public function model()
{
    return User::class;
}
```

To eager load relationships, use the `with()` and return an array of relation(s):
```
public function with()
{
    return ['address', 'post'];
}
```

The following are editable public properties for the table class:

| key  | description | value 
| ------------- | ------------- | ------------- |
| $paginate  | Controls whether the data query & results are paginated. If true, the class must `use WithPagination;`  | true/false
| $pagination  | The number value to paginate with  | integer
| $hasSearch  | Controls global appearance of search bar | true/false
| [$fields](#$fields)  | The fields configuration for your table | array
| [$css](#$css)  | Per-table CSS settings | array

#### $fields
Controls the field configuration for your table

| key  | description | value 
| ------------- | ------------- | ------------- |
| title  | Set the displayed column title  | string
| name  | Should represent the database field name. Use '.' notation for related columns, such as `user.address`  | string
| header_class  | Set a class for the `<th>` tag for this field  | string or null
| cell_class  | Set a class for the `<td>` tag for this field  | string or null
| sortable  | Control whether or not the column is sortable  | bool or null
| searchable  | Control whether or not the column is searchable  | bool or null

#### $css
Used to generate CSS classes when scaffolding the table.

These can be set globally in the configuration file, or on a per-table basis in the component class.

*Note:* CSS classes set in the component will override those from the configuration file where both exist.

| key  | description | value 
| ------------- | ------------- | ------------- |
| wrapper  | CSS class for `<div>` surrounding table  | string or null
| table  | CSS class for `<table>`  | string or null
| thead  | CSS class for `<thead>`  | string or null
| th  | CSS class for `<th>`  | string or null
| tbody  | CSS class for `<tbody>`  | string or null
| tr  | CSS class for `<tr>`  | string or null
| td  | CSS class for `<td>`  | string or null
| search_wrapper  | CSS class for `<div>` surrounding search  | string or null
| search_input  | CSS class for search `<input>`  | string or null
| pagination_wrapper  | CSS class for `<div>` surrounding pagination buttons  | string or null


### Scaffold the table view

When ready, scaffold the table view using the scaffold command:

`php artisan livewire-tables:scaffold UsersTable`

*resources/views/livewire/tables/users-table.blade.php*

```
<div>
    @if ($hasSearch)
    <div>
        <div style="position: relative; display: inline-block;">
        <input type="text" wire:model="search" />
            @if ($search)
                <button wire:click="clearSearch" style="position: absolute; right: 5px;">&#10005;</button>
            @endif
        </div>
    </div>
    @endif
    <table class="table-wrapper">
        <thead>
        <tr>
            <th class="header" wire:click="$emit('sortColumn', 0)">ID</th>
            <th class="header" wire:click="$emit('sortColumn', 1)">Name</th>
            <th class="header bolded" wire:click="$emit('sortColumn', 2)">City</th>
            <th class="header" wire:click="$emit('sortColumn', 3)">Post</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($rowData as $row)
            <tr>
                <td class="table-cell">{{ $row->id }}</td>
                <td class="table-cell">{{ $row->name }}</td>
                <td class="table-cell bolded bg-green">{{ $row->address->city }}</td>
                <td class="table-cell">{{ $row->post->content }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if ($paginate)
    <div>
        {{ $rowData->links() }}
    </div>
    @endif
</div>
```

You can use the scaffold command continuously as you make changes to the parent component class.

Since the rendered template is simple HTML, there’s no need for table “slots” for customization - customize the template as you see fit!

## Todo
- Further support for more advanced queries than a model

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

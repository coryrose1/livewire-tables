<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root namespace for Livewire table component classes in
    | your application. This value effects any livewire-tables file helper commands,
    | like `artisan livewire-tables:make`
    |
    */
    'class_namespace' => 'App\\Http\\Livewire\\Tables',
    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path for Livewire table component views. This effects
    | File manipulation helper commands like `artisan livewire-tables:make`
    |
    */
    'view_path' => resource_path('views/livewire/tables'),
    /*
    |--------------------------------------------------------------------------
    | Default CSS configuration
    |--------------------------------------------------------------------------
    |
    | Use these values to set default CSS classes for the corresponding elements.
    |
    |
    */
    'css' => [
        'wrapper' => null,
        'table' => null,
        'thead' => null,
        'th' => null,
        'tbody' => null,
        'tr' => null,
        'td' => null,
        'search_wrapper' => null,
        'search_input' => null,
        'sorted' => null,
        'pagination_wrapper' => null,
    ],
];

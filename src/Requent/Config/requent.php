<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Query Parameter Name
    |--------------------------------------------------------------------------
    |
    | Here you may define the parameter name to pass your query string
    | to select fields/columns. By default, "fields" is set but you may
    | override it if you wish.
    |
    */
    'query_identifier' => 'fields',

    /*
    |--------------------------------------------------------------------------
    | Paginator Identifier
    |--------------------------------------------------------------------------
    |
    | Here you may define the parameter name to get paginated data.
    | By default, "paginate" is set but you may override it if you wish.
    |
    */
    'paginator_identifier' => 'paginate',

    /*
    |--------------------------------------------------------------------------
    | Default Paginator
    |--------------------------------------------------------------------------
    |
    | Here you may define the default paginator to be used when geting paginated
    | result. By default, the length aware paginator will be used unless you override
    | it here or pass the pagination type in the query string.
    |
    | Available Options: "simple", "default"
    |
    */
    'default_paginator' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Per Page Identifier
    |--------------------------------------------------------------------------
    |
    | Here you may define the name of the query string to pass the number
    | of pages you want to retrieve from paginated result. By default, the
    | package will use "per_page" so you can pass ?per_page=10 to get 10 items
    |  per page unless you override it here.
    */
    'per_page_identifier' => 'per_page',

    /*
    |--------------------------------------------------------------------------
    | Default Attributes Selection
    |--------------------------------------------------------------------------
    |
    | Here you may define whether you would like to load all properties/attributes
    | from a model if no property was explicitly selected using the query string. If
    | you just select relations of a model, the package will load all the attributes
    | by default unless you override it here by setting the value to false.
    */
    'select_default_attributes' => true,
];
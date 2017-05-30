<?php

return [
	
	/*
    |--------------------------------------------------------------------------
    | Query String Parameter Name
    |--------------------------------------------------------------------------
    |
    | Here you may define the parameter name to pass your query string
    | to select fields/columns. By default, "field" is set but you may
    | override it if you wish.
    |
    */
    'fields_parameter_name' => 'fields',

    /*
    |--------------------------------------------------------------------------
    | Search Parameter Name
    |--------------------------------------------------------------------------
    |
    | Here you may define the parameter name for searching anything. 
    | By default, "search" is set but you may override it if you wish.
    |
    */
    'search_parameter_name' => 'search',

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
    | Paginator Per Page Name
    |--------------------------------------------------------------------------
    |
    | Here you may define the name of the query string to pass the number
    | of pages you want to retrieve from paginated result. By default, the
    | package will use "per_page" so you can pass ?per_page=10 to get 10 items
    |  per page unless you override it here.
    */
    'paginator_per_page_name' => 'per_page',

    /*
    |--------------------------------------------------------------------------
    | Default Attributes Selection
    |--------------------------------------------------------------------------
    |
    | Here you may define define whether you would like to load all properties
    | from a model if no property was explicitly selected using the query string.
    | If you just select relations of a model, the package will load all the attributes
    | by default unless you override it here by setting the value to "false/null".
    */
    'select_default_attributes' => true,

    /*
    |--------------------------------------------------------------------------
    | Collection Key
    |--------------------------------------------------------------------------
    |
    | The Laravel framework returns an array of objects when you load collections.
    | Only, the paginated items are wrapped within a "data" key in the result. In
    | this case, this package will allow you to wrap the non-paginated collection
    | into a key. By default, it'll use "data" but you may override it here or you
    | can pass an argument when executing the query using Requent. If, you provide
    | a value here, then by default, this will be used and you can turn it off by
    | simply setting a falsy value here, i.e: null or false.
    |
    */
    'collection_key' => 'data',
];
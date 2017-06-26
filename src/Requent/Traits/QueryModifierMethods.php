<?php

namespace Requent\Traits;

trait QueryModifierMethods
{
    /**
     * Proxy the orderBy method in QueryBuilder
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  String $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function orderBy($query, $value)
    {
        return $query->orderBy($value);
    }

    /**
     * Proxy the orderByDesc method in QueryBuilder
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  String $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function orderByDesc($query, $value)
    {
        return $query->orderBy($value, 'desc');
    }

    /**
     * Proxy the skip method in QueryBuilder
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Int $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function skip($query, $value)
    {
        return $query->skip($value);
    }

    /**
     * Proxy the offset method in QueryBuilder
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Int $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function offset($query, $value)
    {
        return $query->offset($value);
    }

    /**
     * Proxy the take method in QueryBuilder
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Int $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function take($query, $value)
    {
        return $query->take($value);
    }

    /**
     * Proxy the limit method in QueryBuilder
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Int $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function limit($query, $value)
    {
        return $query->limit($value);
    }
}

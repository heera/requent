<?php

namespace Requent\Traits;

trait QueryBuilderMethods
{
    use QueryModifierMethods, TransformerMethods;
    
    /**
     * Proxy to query builder methods (get/find)
     * @param  Mixed $id
     * @param  Array  $columns
     * @return Array (Transformed Result)
     */
    public function fetch($id = null, $columns = ['*'])
    {
        list($id, $columns) = $this->normalizeParameters($id, $columns);
        return $id ? $this->find($id, $columns) : $this->get($columns);
    }

    /**
     * Proxy to query builder's get method
     * @param  Array  $columns
     * @return Array (Transformed Result)
     */
    public function get($columns = ['*'])
    {
        if (($perPage = $this->perPage()) || $this->isPaged()) {
            return $this->callPaginate($perPage, $columns);
        }
        return $this->transform($this->builder->get($columns));
    }

    /**
     * Proxy to query builder's paginate method
     * @param  Mixed $perPage
     * @param  Array  $columns
     * @return Array (Transformed Result)
     */
    public function paginate($perPage = null, $columns = ['*'])
    {
        $perPage = $perPage ?: $this->perPage();
        list($perPage, $columns) = $this->normalizeParameters($perPage, $columns);
        $result = $this->builder->paginate($perPage, $columns);
        $result->appends($this->getQueryStringValue());
        return $this->transform($result);
    }

    /**
     * Proxy to query builder's simplePaginate method
     * @param  Mixed $perPage
     * @param  Array  $columns
     * @return Array (Transformed Result)
     */
    public function simplePaginate($perPage = null, $columns = ['*'])
    {
        $perPage = $perPage ?: $this->perPage();
        list($perPage, $columns) = $this->normalizeParameters($perPage, $columns);
        $result = $this->builder->simplePaginate($perPage, $columns);
        $result->appends($this->getQueryStringValue());
        return $this->transform($result);
    }

    /**
     * Proxy to query builder's findOrFail method
     * @param  Int $id
     * @param  Array  $columns
     * @return Array (Transformed Result)
     */
    public function find($id, $columns = ['*'])
    {
        $result = $this->builder->findOrFail($id, $columns);
        return $this->transform($result);
    }

    /**
     * Proxy to query builder's first method
     * @param  Array  $columns
     * @return Array (Transformed Result)
     */
    public function first($columns = ['*'])
    {
        $result = $this->builder->firstOrFail($columns);
        return $this->transform($result);
    }

    /**
     * Check if paginated result was asked
     * @return Boolean
     */
    protected function isPaged()
    {
        return isset($this->getQueryStringValue()[
            $this->getConfigValue('paginator_identifier')
        ]);
    }

    /**
     * Check if the paginated result is required
     * and find out the amount for the per page
     * @return Mixed
     */
    protected function perPage()
    {
        $page = $this->getQueryStringValue('perpage');
        $page = $page ?: $this->getQueryStringValue('perPage');
        $page = $page ?: $this->getQueryStringValue('per_page');
        $page = $page ?: $this->getQueryStringValue(
            $this->getConfigValue('per_page_identifier')
        );
        return $page;
    }

    /**
     * Call the appropriate pagination method
     * @param  Int $perPage
     * @param  Array $columns
     * @return Mixed
     */
    protected function callPaginate($perPage, $columns)
    {
        if ($this->getPaginator() != 'simple') {
            return $this->paginate($perPage, $columns);
        }
        return $this->simplePaginate($perPage, $columns);
    }

    /**
     * Determine the paginator to be used
     * @return Array
     */
    protected function getPaginator()
    {
        $paginator = $this->getQueryStringValue(
            $this->getConfigValue('paginator_identifier')
        );
        return $paginator ?: $this->getConfigValue('default_paginator');
    }

    /**
     * Normalize the method parameters
     * @param  Int $perPage
     * @param  Mixed $columns
     * @return Array
     */
    protected function normalizeParameters($perPage, $columns = null)
    {
        $columns = is_array($perPage) ? $perPage : $columns;
        $perPage = is_array($perPage) ? null : $perPage;
        return [$perPage, $columns];
    }
}

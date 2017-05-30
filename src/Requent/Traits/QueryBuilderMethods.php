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
        if ($perPage = $this->paged()) {
            $pagination = $this->getQueryStringValue('pagination');
            if($pagination = $pagination ?: $this->getConfigValue('default_paginator')) {
                return $this->callPaginate($pagination, $perPage, $columns);
            }
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
        $perPage = $perPage ?: $this->paged();
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
        $perPage = $perPage ?: $this->paged();
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
     * Check if the paginated result is required
     * and find out the amount for the per page 
     * @return Mixed
     */
    protected function paged()
    {
        $page = $this->getQueryStringValue('perpage');
        $page = $page ?: $this->getQueryStringValue('perPage');
        $page = $page ?: $this->getQueryStringValue('per_page');
        $page = $page ?: $this->getQueryStringValue(
            $this->getConfigValue('paginator_per_page_name')
        );
        return $page;
    }

    /**
     * Call the appropriate pagination method
     * @param  String $pagination Pagination Type
     * @param  Int $perPage
     * @param  Array $columns
     * @return Mixed
     */
    protected function callPaginate($pagination, $perPage, $columns)
    {
        if($pagination != 'simple') {
            return $this->paginate($perPage, $columns);
        }
        return $this->simplePaginate($perPage, $columns);
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
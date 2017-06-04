<?php

namespace Requent\Transformer;

use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseTransformer
{
	/**
     * Config
     * @var Array
     */
    protected $config = null;

    /**
     * Set config
     * @param Array $config
     */
    public function setConfig(Array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get config item
     * @param  String $key
     * @return Mixed
     */
    protected function getConfigValue($key = null)
    {
        if(!$key) return $this->config;

        if(isset($this->config[$key])) {
            return $this->config[$key];
        }
    }

    /**
     * Determine whether the data is paginated
     * @param  Mixed $data
     * @return Boolean
     */
    protected function isPaginated($data)
    {
        return ($data instanceof Paginator || $data instanceof LengthAwarePaginator);
    }

    /**
     * Transform the result given by QueryBuilder
     * 
     * @param  Mixed $result
     * @param  Array $transformer
     * @return Array
     */
    public function transformResult($result, $transformer)
    {
        if ($result instanceof Model) {
            return $this->transformItem($result, $transformer);
        } elseif ($result instanceof Collection) {
            return $this->transformItems($result, $transformer);
        } elseif ($this->isPaginated($result)) {
            return $this->transformPaginatedItems($result, $transformer);
        }
        return $result;
    }
}
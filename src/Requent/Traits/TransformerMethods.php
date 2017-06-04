<?php

namespace Requent\Traits;

use Requent\Transformer\Transformer;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait TransformerMethods
{
    /**
     * Transform the query result using the right transformer
     * @param  Mixed $result
     * @return Array Transformed Result
     */
    protected function transform($result)
    {
        if($this->original) return $result;

        $transformed = $this->resolveTransformer()->transformResult(
            $result, $this->getSelectables($this->selectedColumns)
        );

        if(!$this->isPaginated($result)) {
            if($resourceKey = $this->getResourceKey()) {
                return [$resourceKey => $transformed];
            }
        }

        return $transformed;
    }

    /**
     * Resolve the transformer
     * @return Mixed
     */
    protected function resolveTransformer()
    {
        if(is_string($this->transformer)) {
            $this->transformer = new $this->transformer;
        }

        return $this->transformer->setConfig($this->getConfigValue());
    }

    /**
     * Get the selectable fields/columns
     * @param  Mixed $selectables
     * @return Mixed
     */
    protected function getSelectables($selectables)
    {
        if($this->transformer instanceof Transformer) {
            return $this->transformer;
        }
        
        return $this->getFieldsForDefaultTransformer($selectables);
    }

    /**
     * Get selectables for DefaultTransformer
     * @param  Array $selectables
     * @return Array
     */
    protected function getFieldsForDefaultTransformer($selectables)
    {
        $paramName = $this->getConfigValue('query_identifier');
        foreach($selectables as $field => $value) {
            if($value !== true) {
                if(isset($value[$paramName])) {
                    if(!empty($value[$paramName])) {
                        $selectables[$field] = $this->{__FUNCTION__}($value[$paramName]);
                    } else {
                        unset($selectables[$field]);
                    }
                } else {
                    $selectables[$field] = $field;
                }
            }
        }
        return $selectables;
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
     * Get the key name to wrap the collection
     * @return String
     */
    protected function getResourceKey()
    {
        return $this->resourceKey;
    }
}
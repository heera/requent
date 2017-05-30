<?php

namespace Requent\Traits;

use Requent\Transformer\DefaultTransformer;
use Illuminate\Database\Eloquent\Collection;

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

        $transformed = $this->resolveTransformer()->transformResult($result);
        if($result instanceof Collection) {
            if($resourceKey = $this->getCollectionKey()) {
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
        if($transformer = $this->transformer) {
            return is_string($transformer) ? new $transformer : $transformer;
        }
        return new DefaultTransformer(
            $this->getConfigValue(),
            $this->getSelectables($this->selectedColumns)
        );
    }

    /**
     * Get the selectable fields/columns
     * @param  Array $selectables
     * @return Array
     */
    protected function getSelectables($selectables)
    {
        $paramName = $this->getConfigValue('fields_parameter_name');
        foreach($selectables as $field => $value) {
            if($value !== true) {
                if(isset($value[$paramName])) {
                    if(!empty($value[$paramName])) {
                        $selectables[$field] = $this->getSelectables($value[$paramName]);                 
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
     * Get the key name to wrap the collection
     * @return String
     */
    protected function getCollectionKey()
    {
        return $this->collectionKey;
    }
}
<?php

namespace Requent\Transformer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class Transformer extends BaseTransformer
{
    /**
     * Abstract method to be implemented bt developer
     * to define a explicit custom transformer for data.
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return Array Transformed Result
     */
    abstract public function transform($model);

    /**
     * Transforms paginated result.
     *
     * @param Illuminate\Pagination\SimplePaginator|LengthAwarePaginator $result
     * @param Requent\Transformer\Transformer
     * @return Array
     */
    public function transformPaginatedItems($result, $transformer)
    {
        $result->getCollection()->transform(function ($model) use ($transformer) {
            return $this->transformItem($model, $this->make($transformer));
        });
        return $result->toArray();
    }
    
    /**
     * Transforms eloquent collection.
     *
     * @param Illuminate\Database\Eloquent\Collection $collection
     * @param Requent\Transformer\Transformer
     * @return Array
     */
    public function transformItems($result, $transformer)
    {
        $result->transform(function ($model) use ($transformer) {
            return $this->transformItem($model, $this->make($transformer));
        });
        return $result->toArray();
    }

    /**
     * Transform the eloquent model.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @param Requent\Transformer\Transformer $transformer
     * @return Array
     */
    public function transformItem($model, $transformer)
    {
        if (!$model || is_array($model)) {
            return $model;
        }
        $transformed = $this->make($transformer)->transform($model);
        return $this->transformRelations(
            $model, $this->make($transformer), $transformed
        );
    }

    /**
     * Transform the model relations
     * @param  Illuminate\Database\Eloquent\Model $model
     * @param  Requent\Transformer\Transformer
     * @param  Array $transformed
     * @return Array Transformed
     */
    protected function transformRelations($model, $transformer, $transformed)
    {
        foreach ($model->getRelations() as $key => $relation) {
            if (method_exists($transformer, $key)) {
                $transformed[snake_case($key)] = $this->transformRelation(
                    $transformer, $key, $relation
                );
            }
        }
        return $transformed;
    }

    /**
     * Transform a single relation from the model
     * @param  Requent\Transformer\Transformer
     * @param  String $method
     * @param  Mixed $relation
     * @return Mixed
     */
    protected function transformRelation($transformer, $method, $relation)
    {
        return $relation ? $transformer->{$method}($relation) : $relation;
    }

    /**
     * Resolve the transformer passed from other transformer
     * @param  String $transformer
     * @return $this
     */
    protected function make($transformer)
    {
        return is_string($transformer) ? new $transformer : $transformer;
    }

    /**
     * Catch all missing methods
     * @param  String $method
     * @param  Array $params
     * @return Mixed
     * @throws TransformerException
     */
    public function __call($method, $params)
    {
        if (in_array($method, ['item', 'items'])) {
            $method = 'transform'.studly_case($method);
            return $this->{strtolower($method)}(...$params);
        }

        throw new TransformerException('Undefined method '.get_called_class().':'.$method);
    }
}

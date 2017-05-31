<?php

namespace Requent\Transformer;

use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Transformer
{
    /**
     * Abstract method to be implemented bt developer
     * to define a explicit custom transformer for data.
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return Array Transformed Result
     */
    public abstract function transform($model);

    /**
     * Transform the result came from QueryBuilder.
     *
     * @param Mixed $model
     * @return Mixed
     */
    public function transformResult($result)
    {
        if ($result instanceof Model) {
            return $this->transformItem($result, $this);
        } elseif ($result instanceof Collection) {
            return $this->transformItems($result, $this);
        } elseif ($this->isPaginated($result)) {
            return $this->transformPaginatedItems($result, $this);
        }
        return $result;
    }

    /**
     * Transforms paginated result.
     *
     * @param Illuminate\Pagination\SimplePaginator|LengthAwarePaginator $result
     * @param Requent\Transformer\Transformer
     * @return Array
     */
    public function transformPaginatedItems($result, $transformer)
    {
        $transformer = $this->resolveTransformer($transformer);
        $result->getCollection()->transform(function ($model) use ($transformer) {
            return $this->transformItem($model, $transformer);
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
        $transformer = $this->resolveTransformer($transformer);
        $result->transform(function ($model) use ($transformer) {
            return $this->transformItem($model, $transformer);
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
        $transformer = $this->resolveTransformer($transformer);
        if(!$model || is_array($model)) return $model;
        $transformed = $transformer->transform($model);
        return $this->transformRelations($model, $transformer, $transformed);
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
            if(method_exists($transformer, $key)) {
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
     * Determine whether the data is paginated
     * @param  Mixed $data
     * @return Boolean
     */
    protected function isPaginated($data)
    {
        return ($data instanceof Paginator || $data instanceof LengthAwarePaginator);
    }

    /**
     * Resolve the transformer passed from other transformer
     * @param  String $transformer
     * @return $this
     */
    protected function resolveTransformer($transformer)
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
        if(in_array($method, ['item', 'items'])) {
            $method = 'transform'.studly_case($method);
            return $this->{strtolower($method)}(...$params);
        }

        throw new TransformerException('Undefined method '.get_called_class().':'.$method);
    }
}
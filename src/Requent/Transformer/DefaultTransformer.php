<?php

/**
 * This is the default transformer which will transform
 * the result without any filtering/transformation by user.
 */

namespace Requent\Transformer;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DefaultTransformer
{
	/**
	 * Selectable fields/columns
	 * @var Mixed
	 */
	protected $selectables = null;

	/**
	 * Construct the object
	 * @param Mixed $selectables
	 */
	public function __construct(Array $config, Array $selectables)
	{
        $this->config = $config;
		$this->selectables = $selectables;
	}

	/**
	 * Transform the result given by QueryBuilder
	 * @param  Mixed $result
	 * @param  Mixed $selectables
	 * @return Array
	 */
	public function transformResult($result, $selectables = null)
	{
        $selectables = $selectables ?: $this->selectables;
        if ($result instanceof Model) {
            return $this->transformItem($result, $selectables);
        } elseif ($result instanceof Collection) {
            return $this->transformItems($result, $selectables);
        } elseif ($this->isPaginated($result)) {
            return $this->transformPaginatedItems($result, $selectables);
        }
        return $result;
	}

	/**
	 * Transform a single item/model
	 * @param  Mixed $model
	 * @param  Mixed $selectables
	 * @return Mixed
	 * @throws Requent\Transformer\TransformerException
	 */
	protected function transformItem($model, $selectables)
	{
		if(is_array($model)) return $model;

        // If nothing was selected through query string
        if(!$selectables || !is_array($selectables)) {
            return $model->toArray();
        }

        $selectables = $this->getSelectbles($selectables, $model);
        foreach ($selectables as $key => $value) {
            if ($this->hasAttribute($model, $key)) {
                $result[$key] = $this->getAttribute($model, $key);
            } elseif ($model->relationLoaded($key)) {
                $fields = is_array($selectables[$key]) ? $selectables[$key] : $key;
                $result[snake_case($key)] = $this->transformResult($model[$key], $fields);
            } else {
                throw new TransformerException('Unavailable entity "'.$key.'".');
            }
        }

        return $result;
	}

	/**
	 * Transform a collection
	 * @param  Illuminate\Database\Eloquent\Collection $result
	 * @param  Mixed $selectables
	 * @return Array
	 */
	protected function transformItems($result, $selectables)
	{
		$result->transform(function ($model) use ($selectables) {
            return $this->transformItem($model, $selectables);
        });
        return $result->toArray();
	}

	/**
	 * Transform paginated items
	 * @param  Mixed $result
	 * @param  Mixed $selectables
	 * @return Array
	 */
	protected function transformPaginatedItems($result, $selectables)
	{
		$result->getCollection()->transform(function ($model) use ($selectables) {
            return $this->transformItem($model, $selectables);
        });
        return $result->toArray();
	}

	/**
	 * Check if a given attribute is available
	 * @param  Illuminate\Database\Eloquent\Model $model
	 * @param  String $key
	 * @return Boolean
	 */
	protected function hasAttribute($model, $key)
    {
        return array_key_exists(
            $key, $model->getAttributes()
        ) || $model->hasGetMutator($key);
    }

    /**
     * Get an attribute from the model
     * @param  Illuminate\Database\Eloquent\Model $model
     * @param  String $key
     * @return Mixed
     */
    protected function getAttribute($model, $key)
    {
        $attribute = $model->getAttributeValue($key);
        if($attribute instanceof Carbon) {
            return $attribute->toDateTimeString();
        }
        return $attribute;
    }

    /**
     * Select all attributes if none selected depending on the config
     * @param  Array $selectables
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return Array
     */
    protected function getSelectbles($selectables, $model)
    {
    	if($this->getConfigValue('select_default_attributes')) {
    		if(!array_diff_key($selectables, $model->getRelations())) {
	    		return array_merge($model->getAttributes(), $selectables);
	    	}
    	}
    	return $selectables;
    }

    /**
     * Determine whether the result came form QueryBuilder is paginated
     * @param  Mixed $data
     * @return Boolean
     */
    protected function isPaginated($data)
    {
        return ($data instanceof Paginator || $data instanceof LengthAwarePaginator);
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
}
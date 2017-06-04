<?php

namespace Requent\Transformer;

use Requent\Transformer\Transformer;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait TransformerHelper
{
	/**
	 * A helper method to transform plain result
	 * @param  Mixed $result
	 * @param  instanceof Requent\Transformer\Transformer $transformer
	 * @param  string $resourceKey
	 * @return Array
	 */
	protected function transform($result, $transformer, $resourceKey = null)
	{
		$transformer = is_string($transformer) ? new $transformer : $transformer;
		$transformed = $transformer->transformResult($result, $transformer);
		if(!$this->isPaginated($result) && !is_null($resourceKey)) {
			return [$resourceKey => $transformed];
        }
        return $transformed;
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
}
<?php

namespace Requent\Transformer;

use Requent\Transformer\Transformer;
use Illuminate\Database\Eloquent\Collection;

trait TransformerHelper
{
	/**
	 * A helper method to transform plain result
	 * @param  Mixed $result
	 * @param  instanceof Requent\Transformer\Transformer $transformer
	 * @param  string $resourceKey
	 * @return Array
	 */
	protected function transform($result, $transformer, $resourceKey = 'data')
	{
		$transformer = is_string($transformer) ? new $transformer : $transformer;
		$transformed = $transformer->transformResult($result);
		if($result instanceof Collection) {
			return [$resourceKey => $transformed];
        }
        return $transformed;
	}
}
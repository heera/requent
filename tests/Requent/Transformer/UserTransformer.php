<?php

namespace Requent\Transformer;

use Requent\Transformer\Transformer as BaseTransformer;
use Requent\Transformer\PostTransformer;

class UserTransformer extends BaseTransformer
{
	public function transform($model)
	{
		return [
			'name' => $model->name,
			'email' => $model->email
		];
	}

	public function posts($collection)
	{
		return $this->items($collection, PostTransformer::class);
	}
}
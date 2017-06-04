<?php

namespace Requent\Transformer;

use Requent\Transformer\Transformer as AbstractTransformer;
use Requent\Transformer\PostTransformer;

class UserTransformer extends AbstractTransformer
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
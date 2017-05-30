<?php

namespace Requent\Transformer;

use Requent\Transformer\Transformer as BaseTransformer;

class PostTransformer extends BaseTransformer
{
	public function transform($model)
	{
		return [
			'title' => $model->title,
			'body' => $model->body
		];
	}
}
<?php

namespace Requent\Transformer;

use Requent\Transformer\Transformer as AbstractTransformer;

class PostTransformer extends AbstractTransformer
{
	public function transform($model)
	{
		return [
			'title' => $model->title,
			'body' => $model->body
		];
	}
}
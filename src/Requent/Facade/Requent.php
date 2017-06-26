<?php

namespace Requent\Facade;

use Illuminate\Support\Facades\Facade;

class Requent extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'requent';
    }
}

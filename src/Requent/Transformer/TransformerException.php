<?php

namespace Requent\Transformer;

use Exception;

class TransformerException extends Exception
{
    private $previous;
    
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code);
        
        if (!is_null($previous)) {
            $this->previous = $previous;
        }
    }
}

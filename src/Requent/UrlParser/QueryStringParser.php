<?php

namespace Requent\UrlParser;

class QueryStringParser
{
    public static function parse($input, $paramName = 'fields')
    {
        return (new static)->parseValue(str_split($input), $paramName);
    }

    protected function parseValue(Array $chars, $paramName = 'fields')
    {
        $end = count($chars);
        $current = '';
        $output = [];
        $i = 0;
        while ($i < $end) {
            $char = $chars[$i];
            /*
             * if name followed by a ',', it's the end of a field
             * if name followed by a '{', treat it as fields
             * if name followed by a '.', treat it as [order] or [limit] or [offset] modifier
             */
            switch ($char) {
                case ',':
                    $trimmedName = trim($current);
                    if ($trimmedName && !array_key_exists($trimmedName, $output)) {
                        $output[$trimmedName] = true;
                    }
                    $current = '';
                    break;
                case '{':
                    $trimmedName = trim($current);
                    $nestedLength = $this->getNestedLength($chars, $i);
                    $output[$trimmedName] = array_key_exists($trimmedName, $output) ? $output[$trimmedName] : [];
                    $nested = array_slice($chars, $i + 1, $nestedLength - 2);
                    $output[$trimmedName][$paramName] = $this->parseValue($nested, $paramName);
                    $current = '';
                    $i += $nestedLength;
                    break;
                case '.':
                    $trimmedName = trim($current);
                    $methodLength = $this->getMethodLength($chars, $i);
                    $output[$trimmedName] = array_key_exists($trimmedName, $output) ? $output[$trimmedName] : [];
                    $method = array_slice($chars, $i + 1, $methodLength - 1);
                    list($methodName, $param) = $this->parseMethod($method, $paramName);
                    $output[$trimmedName][$methodName] = $param;
                    $i += $methodLength - 1;
                    break;
                case '}':
                    $this->getNestedLength($chars, $i);
                default:
                    $current .= $char;
            }
            $i++;
        }
        $trimmedName = trim($current);
        if ($trimmedName && !array_key_exists($trimmedName, $output)) {
            $output[$trimmedName] = true;
        }
        return $output;
    }

    protected function getNestedLength(Array $chars, $offset)
    {
        $nested = 0;
        $end = count($chars);
        $i = $offset;
        while ($i < $end) {
            $char = $chars[$i];
            switch ($char) {
                case '{';
                    $nested++;
                    break;
                case '}':
                    $nested--;
                    if ($nested === 0) {
                        return $i + 1 - $offset;
                    }
                    break;
            }
            $i++;
        }
        throw new \Exception('Nested operator {} do not match');
    }

    protected function getMethodLength(Array $chars, $offset)
    {
        $pos = strpos(join($chars), ')', $offset);
        if ($pos > $offset) {
            return $pos - $offset + 1;
        } else {
            throw new \InvalidArgumentException('Method operator () do not match');
        }
    }

    protected function parseMethod(Array $chars, $paramName)
    {
        try {
            $str = join($chars);
            $re = '/(\w+)\(([-\w]+)\)/';
            preg_match_all($re, $str, $matches);
            $methodName = $matches[1][0];
            $param = $matches[2][0];
            switch ($methodName) {
                case $paramName:
                    throw new \Exception("Invalid method \"$methodName\" name");
                default:
                    return [$methodName, $param];
            }
        } catch(\Exception $e) {
            throw new \InvalidArgumentException('Method operator () do not match');
        }
    }
}
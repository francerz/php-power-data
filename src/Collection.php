<?php
namespace Francerz\PowerData;

use Exception;
use Francerz\PowerData\Exceptions\InvalidOffsetException;

class Collection implements
    \ArrayAccess,
    \Countable,
    \Iterator
{
    private $data;

    public function __construct($data = array())
    {
        $this->data = $data;
    }
    #region \ArrayAccess implementation
    public function offsetExists($offset)
    {
        if (isset($this->data[$offset])) {
            return true;
        }
        if (array_key_exists($offset, $this->data)) {
            return true;
        }
        return false;
    }
    public function offsetGet($offset)
    {
        if (is_array($offset)) {
            // Multidimensional data access support.
            return $this->offsetGetArray($offset);
        }
        if (!is_int($offset)) {
            throw new InvalidOffsetException("Collection->offsetGet(): invalid offset value");
        }
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }
        return;
    }
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
            return;
        }
        if (!is_int($offset)) {
            throw new InvalidOffsetException("Invalid collection offset.");
        }
        $this->data[$offset] = $value;
    }
    public function offsetUnset($offset)
    {
        if (!is_int($offset)) {
            throw new InvalidOffsetException("Invalid collection offset.");
        }
        unset($this->data[$offset]);
    }
    #endregion

    #region \Countable implementation
    public function count()
    {
        return count($this->data);
    }
    #endregion
    
    #region \Iterator implementation
    public function current()
    {
        return current($this->data);
    }
    public function key()
    {
        return key($this->data);
    }
    public function next()
    {
        return next($this->data);
    }
    public function rewind()
    {
        return reset($this->data);
    }
    public function valid()
    {
        return key($this->data) !== null;
    }
    #endregion

    private function offsetGetArray(array $offset)
    {
        if (empty($offset)){
            throw new InvalidOffsetException('Collection->offsetGetArray(): empty offset not allowed.');
        }
        if (Arrays::hasNumericKeys($offset)) {
            throw new InvalidOffsetException('Collection->offsetGetArray(): offset must not contain numeric keys.');
        }
        
    }
    public function filter(callable $callback)
    {
        try {
            $data = [];
            foreach($this as $key => $value)
            {
                if ($callback($value, $key)) {
                    $data[] = $value;
                }
            }
            return new Collection($data);
        } catch(Exception $ex) {
            throw new PowerDataException($ex->getMessage(), 0, $ex);
        }
    }
}

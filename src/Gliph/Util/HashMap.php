<?php

namespace Gliph\Util;

/**
 * Some pieces borrowed from...
 * @link https://github.com/morrisonlevi/Ardent/blob/master/src/Ardent/HashMap.php
 *
 * (because we don't want a PHP 5.4 dependency just yet)
 */
class HashMap implements \ArrayAccess, \Countable {
    protected $k, $v = array();

    public function offsetExists($offset) {
        return isset($this->k[$this->hash($offset)]);
    }

    public function offsetGet($offset) {
        return $this->v[$this->hash($offset)];
    }

    public function offsetUnset($offset) {
        $k = $this->hash($offset);
        unset($this->k[$k], $this->v[$k]);
    }

    public function offsetSet($offset, $value) {
        $k = $this->hash($offset);
        $this->k[$k] = $offset;
        $this->v[$k] = $value;
    }

    public function &get($offset) {
        return $this->v[$this->hash($offset)];
    }

    public function keys() {
        return array_values($this->k);
    }

    public function values() {
        return array_values($this->v);
    }

    public function count() {
        return count($this->k);
    }

    public function hash($item) {
        if (is_object($item)) {
            return spl_object_hash($item);
        } elseif (is_resource($item)) {
            return "r_$item";
        } elseif (is_array($item)) {
            return 'a_' . md5(serialize($item));
        } elseif (isset($this->k[$item])) {
            // This ensures we don't doublehash a string.
            return $item;
        } elseif (is_scalar($item)) {
            return "s_$item";
        }

        return '0';
    }

    public function pair() {
        return array(current($this->k), current($this->v));
    }

    public function current() {
        return current($this->v);
    }

    public function next() {
        next($this->k);
        next($this->v);
    }

    public function key() {
        return current($this->k);
    }

    public function valid() {
        return key($this->k) !== NULL;
    }

    public function rewind() {
        reset($this->k);
        reset($this->v);
    }

}

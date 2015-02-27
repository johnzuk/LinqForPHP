<?php

namespace LinqForPHP\Linq;


use LinqForPHP\Linq\Exceptions\InvalidIteratorException;
use LinqForPHP\Linq\Iterators\ConcatIterator;
use LinqForPHP\Linq\Iterators\SelectIterator;
use LinqForPHP\Linq\Iterators\WhereIterator;

class Enumerable implements \IteratorAggregate, \Countable
{

    protected $iterator;

    function __construct($iterator)
    {
        if(is_array($iterator))
        {
            $this->iterator = new \ArrayIterator($iterator);
        }
        else if($iterator instanceof \Iterator)
        {
            $this->iterator = $iterator;
        }
        else
        {
            throw new InvalidIteratorException("Iterator must be instance od Iterator or array");
        }

    }

    public function all(callable $func = null)
    {

    }

    public function aggregate(callable $callback, $init = null)
    {
        return array_reduce(iterator_to_array($this->iterator,false), $callback, $init);
    }

    public function any(callable $func = null)
    {

    }

    public function select(callable $callback)
    {
        return new Enumerable(new SelectIterator($this->iterator,$callback));
    }

    public function where(callable $callback)
    {
        return new Enumerable(new WhereIterator($this->iterator,$callback));
    }

    public function distinct()
    {
        return new Enumerable(new \ArrayIterator(array_unique(iterator_to_array($this->iterator,false))));
    }

    public function order(callable $func = null)
    {

    }

    public function min()
    {

    }

    public function max()
    {

    }

    public function average()
    {

    }

    public function concat(Enumerable $iterator)
    {
        return new Enumerable(new ConcatIterator($iterator,$this));
    }

    /**
     * @param int $start
     * @param int $end
     * @param int $step
     * @return Enumerable
     */
    public static function range($start, $end, $step = 1)
    {
        //low memory usage than range function
        $range = function() use($start, $end, $step){
            for($i = $start; $i<=$end; $i+=$step)
            {
                yield $i;
            }
        };

        return new Enumerable($range());
    }

    public function each(callable $callback)
    {
        array_walk(iterator_to_array($this->iterator,false),$callback);
    }

    #region override methods

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        if($this->iterator instanceof \Countable) {
            return $this->iterator->count();
        }
        return iterator_count($this->iterator);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    #endregion
}
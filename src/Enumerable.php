<?php
namespace LinqForPHP\Linq;

use LinqForPHP\Linq\Exceptions\InvalidIteratorException;
use LinqForPHP\Linq\Exceptions\InvalidOperationException;
use LinqForPHP\Linq\Iterators\ConcatIterator;
use LinqForPHP\Linq\Iterators\SelectIterator;
use LinqForPHP\Linq\Iterators\WhereIterator;

class Enumerable implements \IteratorAggregate
{
    protected $iterator;
    protected $default = 0;

    function __construct($iterator)
    {
        if (is_array($iterator)) {
            $this->iterator = new \ArrayIterator($iterator);
        } else if($iterator instanceof \Iterator) {
            $this->iterator = $iterator;
        } else {
            throw new InvalidIteratorException("Iterator must be instance od Iterator or array");
        }
    }

    public function aggregate(callable $callback, $seed = null, callable $resultSelector = null)
    {
        $result = $seed;
        if (!is_null($seed)) {
            foreach ($this as $element) {
                $result = $callback($result, $element);
            }
        } else {
            $assigned = false;
            foreach ($this as $element) {
                if ($assigned) {
                    $result = $callback($result, $element);
                } else {
                    $assigned = true;
                    $result = $element;
                }
            }
        }
        if (!is_null($resultSelector)) {
            $result = $resultSelector($result);
        }

        return $result;
    }

    public function all(callable $callback)
    {
        foreach ($this as $element) {
            if (!$callback($element)) {
                return false;
            }
        }
        return true;
    }

    public function any(callable $callback = null)
    {
        if ($callback === null) {
            $callback = $this->getTrueCallback();
        }

        foreach ($this as $element) {
            if ($callback($element)) {
                return true;
            }
        }
        return false;
    }

    public function average(callable $callback)
    {
        //todo implement this function
    }

    public function concat(Enumerable $iterator)
    {
        //todo change to can get array or iterator
        $concat = function () use ($iterator) {
            foreach ($this as $element) {
                yield $element;
            }
            foreach ($iterator as $element) {
                yield $element;
            }

        };

        return new Enumerable($concat());
    }

    public function contains($compare, callable $comparer = null)
    {
        if ($comparer === null) {
            $comparer = $this->getDefaultCoparer();
        }
        foreach ($this as $element) {
            if ($comparer($compare, $element)) {
                return true;
            }
        }

        return false;
    }

    public function count(callable $callback = null)
    {
        if (!is_null($callback)) {
            $count = 0;
            foreach ($this as $item) {
                if ($callback($item)) {
                    $count++;
                }
            }
            return $count;
        }

        return count($this->iterator);
    }

    public function defaultIfEmpty($defaultValue = null)
    {
        if ($defaultValue !== null) {
            $this->default = $defaultValue;
        }
        if (empty($this->toArray())) {
            return new Enumerable([$this->default]);
        }
        return new Enumerable($this->iterator);
    }

    public function distinct()
    {
        //todo ad distinct with callback
        $distinct = function () {
            $distinctElement = [];
            foreach ($this as $element) {
                if (!in_array($element, $distinctElement)) {
                    $distinctElement[] = $element;
                    yield $element;
                }
            }

        };

        return new Enumerable($distinct());
    }

    public function first(callable $callback = null)
    {
        if (is_null($callback)) {
            $callback = $this->getTrueCallback();
        }
        foreach ($this as $element) {
            if ($callback($element)) {
                return $element;
            }
        }
        throw new InvalidOperationException();
    }

    public function firstOrDefault(callable $callback = null)
    {
        if (is_null($callback)) {
            $callback = $this->getTrueCallback();
        }
        foreach ($this as $element) {
            if ($callback($element)) {
                return $element;
            }
        }
        return $this->default;
    }

    public function single(callable $callback = null)
    {
        if (is_null($callback)) {
            $callback = $this->getTrueCallback();
        }

        $found = false;
        foreach ($this as $element) {
            if ($found) {
                throw new InvalidOperationException();
            }
            if ($callback($element)) {
                $found = true;
            }
        }
        if (!$found) {
            //todo change throw exception
            throw new \Exception();
        }
        return $found;
    }

    public function select(callable $callback)
    {
        $select = function () use ($callback) {
            foreach ($this as $item) {
                yield $callback($item);
            }

        };

        return new Enumerable($select());
    }

    public function where(callable $callback)
    {
        $where = function () use ($callback) {
            foreach ($this->iterator as $item) {
                if ($callback($item)) {
                    yield $item;
                }
            }
        };

        return new Enumerable($where());
    }

    public function take($count)
    {
        return new Enumerable(new \LimitIterator($this->iterator, 0, $count));
    }

    public function takeWhile(callable $callback)
    {
        $take = function () use ($callback) {
            foreach ($this as $item) {
                if (!$callback($item)) {
                    break;
                }
                yield $item;
            }
        };

        return new Enumerable($take());
    }

    public function skip($skip)
    {
        /*$skipFnc = function () use ($skip) {
            $i = 0;
            foreach ($this as $element) {
                if ($i++ >= $skip) {
                    yield $element;
                }
            }

        };

        return new Enumerable($skipFnc());*/
        return new Enumerable(new \LimitIterator($this->iterator, $skip));
    }

    public function skipWhile(callable $callback)
    {
        $skip = function () use ($callback) {
            $skipped = false;
            foreach ($this as $item) {
                if (!$skipped && !$callback($item)) {
                    $skipped = true;
                }
                if ($skipped) {
                    yield $item;
                }
            }
        };

        return new Enumerable($skip());
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

    /**
     * @param int $start
     * @param int $end
     * @param int $step
     * @return Enumerable
     */
    public static function range($start, $end, $step = 1)
    {
        $range = function() use ($start, $end, $step) {
            for ($i = $start; $i<=$end; $i+=$step) {
                yield $i;
            }
        };

        return new Enumerable($range());
    }

    public function each(callable $callback)
    {
        array_walk(iterator_to_array($this->iterator,false),$callback);
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

    public function toArray()
    {
        return iterator_to_array($this);
    }

    private function getTrueCallback($result = true)
    {
        return function () use ($result) {
            return $result;
        };
    }

    private function getDefaultCoparer()
    {
        return function ($x, $y) {
            return $x === $y;
        };
    }
}
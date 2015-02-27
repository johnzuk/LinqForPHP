<?php

namespace LinqForPHP\Linq\Iterators;

use LinqForPHP\Linq\Exception\InvalidIteratorException;

class ConcatIterator extends \AppendIterator
{

    function __construct($firstIterator,$secondIterator)
    {
        parent::__construct();
        $this->append($this->getIterator($firstIterator));
        $this->append($this->getIterator($secondIterator));
    }


    private function getIterator($iterator)
    {
        if($iterator instanceof \Iterator)
        {
            return $iterator;
        }
        else if($iterator instanceof \IteratorAggregate)
        {
            return $iterator->getIterator();
        }

        throw new InvalidIteratorException();
    }
}
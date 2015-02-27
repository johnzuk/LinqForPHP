<?php

namespace LinqForPHP\Linq\Iterators;

class WhereIterator extends \FilterIterator
{
    private $callback;

    function __construct(\Iterator $iterator, $callback)
    {
        $this->callback = $callback;
        parent::__construct($iterator);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the current element of the iterator is acceptable
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {
        $callback = $this->callback;
        return $callback(parent::current());
    }
}
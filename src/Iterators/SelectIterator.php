<?php

namespace LinqForPHP\Linq\Iterators;


class SelectIterator extends \IteratorIterator
{
    private $callback;

    function __construct(\Iterator $iterator, $callback)
    {
        parent::__construct($iterator);
        $this->callback = $callback;
    }

    public function current()
    {
        $callback = $this->callback;
        return $callback(parent::current());
    }


}
<?php

namespace LinqForPHP\Linq;

use LinqForPHP\Linq\Exceptions\BadIndexException;

class Sequence extends Enumerable
{
    private $elements = [];

    function __construct($init)
    {
        if(is_array($init))
        {
            $this->elements = $init;
        }
        parent::__construct($this->elements);
    }

    public function add($item)
    {
        if($this->max > 0)
        {
            if($this->count() < $this->max)
            {
                $this->elements[] = $item;
                $this->updateIterator();
                return $this;
            }
            throw new \Exception();
        }
        else
        {
            $this->elements[] = $item;
            $this->updateIterator();
            return $this;
        }

    }

    public function removeAt($index)
    {
        if(isset($this->elements[$index]))
        {
            unset($this->elements[$index]);
            $this->update();
            return $this;
        }

        throw new BadIndexException("Index $index not exist");
    }

    public function remove()
    {

    }

    public function toArray()
    {
        return $this->elements;
    }

    private function updateIterator()
    {
        $this->iterator = new \ArrayIterator($this->elements);
    }

    private function updateElements()
    {
        $this->elements = array_values($this->elements);
    }

    private function update()
    {
        $this->updateElements();
        $this->updateIterator();
    }
}
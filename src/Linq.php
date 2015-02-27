<?php

namespace LinqForPHP\Linq;

class Linq extends Enumerable
{
    public static function from($elements)
    {
        return new Linqu($elements);
    }
}
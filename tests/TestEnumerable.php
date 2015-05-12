<?php

class TestEnumerable extends PHPUnit_Framework_TestCase
{
    public function testConstructorArray()
    {
        $elements = [1, 2, 3, 4, 5, 6];
        $linq = new \LinqForPHP\Linq\Linq($elements);
    }

    public function testConstructorIterator()
    {
        $elements = new ArrayIterator([1, 2, 3, 4, 5, 6]);
        $linq = new \LinqForPHP\Linq\Linq($elements);
    }

    /**
     * @expectedException \LinqForPHP\Linq\Exceptions\InvalidIteratorException
     */
    public function testConstructorException()
    {
        $elements = "test";
        $linq = new \LinqForPHP\Linq\Linq($elements);
    }

    public function testAggregate()
    {
        $text = "the quick brown fox jumps over the lazy dog";
        $elements = explode(" ", $text);
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $aggregate = $linq->aggregate(function ($state, $next) {
            return $next." ".$state;
        });

        $this->assertSame("dog lazy the over jumps fox brown quick the", $aggregate);

    }

    public function testAggregateWithSeed()
    {
        $elements = [4, 8, 8, 3, 9, 0, 7, 8, 2];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $aggregate = $linq->aggregate(function ($total, $next) {
            return $next % 2 == 0 ? $total + 1 : $total;
        }, 0);

        $this->assertSame(6, $aggregate);
    }

    public function testAggregateWithSeedAndResultSelector()
    {
        $elements = ["apple", "mango", "orange", "passionfruit", "grape" ];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $aggregate = $linq->aggregate(function ($longest, $next) {
            return strlen($next) > strlen($longest) ? $next : $longest;
        }, null, function ($n) {
            return strtoupper($n);
        });

        $this->assertSame("PASSIONFRUIT", $aggregate);
    }

    public function testNotAllElementIsOk()
    {
        $elements = ["Bob", "Bart", "Jacob"];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $all = $linq->all(function ($n) {
            return stripos($n, "B") == 0;
        });

        $this->assertFalse($all);
    }

    public function testAllElementIsOk()
    {
        $elements = ["Bob", "Bart", "Benjamin"];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $all = $linq->all(function ($n) {
            return stripos($n, "B") == 0;
        });

        $this->assertTrue($all);
    }

    public function testAnyElementIsInCollection()
    {
        $elements = ["Bob", "Bart", "Benjamin"];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $any = $linq->any();

        $this->assertTrue($any);
    }

    public function testAnyElementIsNoInCollection()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $any = $linq->any();

        $this->assertFalse($any);
    }

    public function testAnyElementInCollectionIsMatch()
    {
        $elements = ["abc", "cde", 3, "bde"];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $any = $linq->any(function ($n) {
            return is_numeric($n);
        });

        $this->assertTrue($any);
    }

    public function testAnyElementInCollectionIsNotMatch()
    {
        $elements = ["abc", "cde", "abc", "bde"];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $any = $linq->any(function ($n) {
            return is_numeric($n);
        });

        $this->assertFalse($any);
    }

    //todo test average

    public function testConcat()
    {
        $cats = ["Bob", "Fido"];
        $dogs = ["Barley", "Boots"];
        $animals = new \LinqForPHP\Linq\Linq($cats);
        $concat = $animals->concat(new \LinqForPHP\Linq\Linq($dogs))->toArray();

        $this->assertSame(["Bob", "Fido", "Barley", "Boots"], $concat);
    }

    public function testCollectionContainsElement()
    {
        $elements = [1, 2, 3, 4, 5, 6];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $contains = $linq->contains(4);

        $this->assertTrue($contains);
    }

    public function testCollectionNotContainsElement()
    {
        $elements = [1, 2, 3, 4, 5, 6];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $contains = $linq->contains(9);

        $this->assertFalse($contains);
    }

    public function testCollectionContainsElementWithCallback()
    {
        $elements = [1, 2, 3, 4, 5, 6];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $contains = $linq->contains(9, function ($compare, $element) {
            return $compare%2 == $element%2;
        });

        $this->assertTrue($contains);
    }

    public function testCollectionNotContainsElementWithCallback()
    {
        $elements = [2, 4, 6, 8];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $contains = $linq->contains(9, function ($compare, $element) {
            return $compare%2 == $element%2;
        });

        $this->assertFalse($contains);
    }

    public function testCountWithoutCallback()
    {
        $elements = [2, 4, 6, 8, 9];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $count = $linq->count();

        $this->assertEquals(5, $count);
    }

    public function testCountWithCallback()
    {
        $elements = [1, 2, 3, 4, 6];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $count = $linq->count(function ($n) {
            return $n%2 == 0;
        });

        $this->assertEquals(3, $count);
    }

    public function testDefaultIfEmptyWhenIsNotEmpty()
    {
        $elements = [1, 2, 3, 4, 6];
        $linq = new \LinqForPHP\Linq\Linq($elements);

        $default = [];
        foreach ($linq->defaultIfEmpty() as $element) {
            $default[] = $element;
        }

        $this->assertSame([1, 2, 3, 4, 6], $default);
    }

    public function testDefaultIfEmptyWhenIsEmpty()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $empty = $linq->defaultIfEmpty()->toArray();

        $this->assertSame([0], $empty);
    }

    public function testDefaultIfEmptyWhenIsEmptyWithDefaultValue()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $default = $linq->defaultIfEmpty(1)->toArray();

        $this->assertSame([1], $default);
    }

    public function testDistinctWithoutCallback()
    {
        $elements = [1, 2, 3, 4, 5, 3, 3, 1, 1, 5];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $distinct = $linq->distinct()->toArray();

        $this->assertSame([1, 2, 3, 4, 5], $distinct);
    }

    public function testSelect()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $linq = new \LinqForPHP\Linq\Enumerable($elements);
        $select = $linq->select(function ($n) {
            return $n+2;
        })->toArray();

        $this->assertSame([3, 4, 5, 6, 7, 8, 9, 10, 11, 12], $select);
    }

   public function testWhere()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $linq = new \LinqForPHP\Linq\Enumerable($elements);
        $select = $linq->where(function ($n) {
            return $n > 4;
        })->toArray();

        $this->assertSame([5, 6, 7, 8, 9, 10], $select);
    }

    /*public function testSkipWhile()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $linq = new \LinqForPHP\Linq\Enumerable($elements);
        $skip = $linq->skipWhile(function ($n) {
            return $n != 4;
        })->toArray();

        $this->assertSame([4, 5, 6, 7, 8, 9, 10], $skip);
    }*/
}

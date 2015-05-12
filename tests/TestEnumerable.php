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

    public function testElementAtExist()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $element = $linq->elementAt(2);

        $this->assertEquals(3, $element);
    }

    /**
     * @expectedException \LinqForPHP\Linq\Exceptions\ArgumentOutOfRangeException
     */
    public function testElementAtNotExist()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $element = $linq->elementAt(12);

        $this->assertEquals(3, $element);
    }

    public function testElementAtOrDefaultElementExist()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $element = $linq->elementAtOrDefault(2);

        $this->assertEquals(3, $element);
    }

    public function testElementAtOrDefaultElementNotExist()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $element = $linq->elementAtOrDefault(12);

        $this->assertEquals(0, $element);
    }

    public function testIsEmptyWhenIsEmpty()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $empty = $linq->isEmpty();

        $this->assertTrue($empty);
    }

    public function testIsEmptyWhenIsNotEmpty()
    {
        $elements = [1, 2, 3, 4];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $empty = $linq->isEmpty();

        $this->assertFalse($empty);
    }

    public function testExcept()
    {
        $elements = [2.0, 2.1, 2.2, 2.3, 2.4, 2.5];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $except = $linq->except(2.2)->toArray();

        $this->assertSame([2.0, 2.1, 2.3, 2.4, 2.5 ], $except);
    }

    public function testExceptWithCallback()
    {
        $elements = [2.0, 2.1, 2.2,"abc", "bcd", 2.3, 2.4, 2.5];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $except = $linq->except("abc", function ($n, $element) {
            return is_string($n) == is_string($element);
        })->toArray();

        $this->assertSame([2.0, 2.1, 2.2, 2.3, 2.4, 2.5 ], $except);
    }

    public function testFirst()
    {
        $elements = [2.0, 2.1, 2.2,"abc", "bcd", 2.3, 2.4, 2.5];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $first = $linq->first();

        $this->assertEquals(2.0, $first);
    }

    public function testFirstWithCallback()
    {
        $elements = [2.0, 2.1, 2.2,"abc", "bcd", 2.3, 2.4, 2.5];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $first = $linq->first(function ($n) {
            return is_string($n);
        });

        $this->assertEquals("abc", $first);
    }

    /**
     * @expectedException \LinqForPHP\Linq\Exceptions\InvalidOperationException
     */
    public function testFirstWithEmptyCollection()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $first = $linq->first();
    }

    public function testFirstOrDefaultEmptyCollection()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $first = $linq->firstOrDefault();

        $this->assertEquals(0, $first);
    }

    public function testFirstOrDefaultNotEmptyCollection()
    {
        $elements = [1, 3, 4, 5];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $first = $linq->firstOrDefault();

        $this->assertEquals(1, $first);
    }

    public function testIntersection()
    {
        $elements = [44, 26, 92, 30, 71, 38];
        $elements2 = [39, 59, 83, 47, 26, 4, 30];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $intersection = $linq->intersection(new \LinqForPHP\Linq\Linq($elements2))->toArray();

        $this->assertSame([26, 30], $intersection);
    }

    public function testLast()
    {
        $elements = [1, 3, 4, 5];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $last = $linq->last();

        $this->assertEquals(5, $last);
    }

    public function testLastWithCallback()
    {
        $elements = [1, 3, "bla", 4, 5, "foo"];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $last = $linq->last(function ($n) {
            return is_string($n);
        });

        $this->assertEquals("foo", $last);
    }

    /**
     * @expectedException \LinqForPHP\Linq\Exceptions\InvalidOperationException
     */
    public function testLastFromEmpty()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $last = $linq->last();
    }

    public function testLastOrDefaultEmptyCollection()
    {
        $elements = [];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $last = $linq->lastOrDefault();

        $this->assertEquals(0, $last);
    }

    public function testLastOrDefaultNotEmptyCollection()
    {
        $elements = [3, 4, 5, 6];
        $linq = new \LinqForPHP\Linq\Linq($elements);
        $last = $linq->lastOrDefault();

        $this->assertEquals(6, $last);
    }

    public function testRepeat()
    {
        $repeat = \LinqForPHP\Linq\Linq::repeat("a", 5)->toArray();

        $this->assertSame(["a", "a", "a", "a", "a"], $repeat);
    }

    /*public function testSelect()
    {
        $elements = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $linq = new \LinqForPHP\Linq\Enumerable($elements);
        $select = $linq->select(function ($n) {
            return $n+2;
        })->toArray();

        $this->assertSame([3, 4, 5, 6, 7, 8, 9, 10, 11, 12], $select);
    }*/

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

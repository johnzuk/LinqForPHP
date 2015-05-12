<?php
$start = microtime(true);

require_once "../vendor/autoload.php";

use LinqForPHP\Linq\Linq;

$test = Linq::range(0, 100000)
    ->where(function ($n) {
        return $n%2;
    })
    ->where(function ($n) {
        return $n > 10;
    })
    ->where(function ($n) {
        return $n < 800;
    })
    ->select(function ($n) {
        return $n*2+1;
    })
    ->skip(10)
    ->take(5);

foreach ($test as $element) {
    echo $element."  ";
}


/*$testArray2 = [9,8,7,6,0];

$test2 = new Linq($testArray2);

$odd = $test->where(function($n){
    return ($n%2);
});

foreach ($odd as $item) {
    echo "$item";
}

$new = $test->select(function($n){
    return $n+2;
})->where(function($n){
    return ($n%2);
});

echo "<br><br>";

echo $new->aggregate(function($current,$n){
    return "$n .".$current;
});

echo "<br><br>";

foreach ($new as $item) {
    echo $item;
}

$concat = $test2->concat($test)->distinct()->each(function($n){
    echo $n;
});*/


$end = microtime(true);
echo "<br><br>";
echo "Time: ".(($end-$start)/60)."<Br>";
echo "Memory ".(memory_get_usage()/1024/1024);

echo "<BR><BR><BR><BR> NOWE: <br><BR>";
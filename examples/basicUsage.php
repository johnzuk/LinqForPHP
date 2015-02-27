<?php
$start = microtime(true);

require_once "../vendor/autoload.php";

use LinqForPHP\Linq\Linq;

$testArray = [1,2,3,4,5,1];

$test = new Linq($testArray);

$testArray2 = [9,8,7,6,0];

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

echo '<br>';

/*echo $new->aggregate(function($current,$n){
    return $current += $n;
});
*/
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
});

/*var_dump(iterator_to_array($concat,false));

echo 'LISTA: <br>';
foreach ($concat as $item) {
    echo $item."<br>";
}*/




$end = microtime(true);
echo "<br><br>";
echo "Time: ".(($end-$start)/60)."<Br>";
echo "Memory ".(memory_get_usage()/1024/1024);

echo "<BR><BR><BR><BR> NOWE: <br><BR>";
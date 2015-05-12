<?php
$start = microtime(true);
require_once "../vendor/autoload.php";

use LinqForPHP\Linq\Sequence;
use LinqForPHP\Linq\Enumerable;

$lists = new Sequence(5);

try {
    $lists
        ->add(1)
        ->add(2)
        ->add(3)
        ->add(4)
        ->add(5)
        ->add(6)
        ->add(7);
}catch (Exception $e)
{
    $e->getMessage();
}

foreach ($lists as $item) {
    echo $item;
}


/*for($i = 4; $i<1000; $i++)
{
    $lists->add($i);
}

$wow = $lists->where(function($n){
    return ($n%2);
})->where(function($n){
    return $n<293;
});


foreach ($wow as $n) {
    echo $n;
}*/

//$memorytest = range(0,100000,1);

$memtest = Enumerable::range(1,1000)
    ->where(function($n){
        return $n < 200;
    })
    ->where(function($n){
        return $n%2;
    });

foreach ($memtest as $n){
    echo $n;
}


$end = microtime(true);
echo "<br><br>";
echo "Time: ".(($end-$start)/60)."<Br>";
echo "Memory ".(memory_get_usage()/1024/1024);
<?php
class A {
    public static $classes;
}
A::$classes = [
'soldier' => [
    'cost' => 100,
    'ratio' => 2,
    'level' => [],
    'need' => [],
],
'archer' => [
    'cost' => 100,
    'ratio' => 2,
    'level' => [],
    'need' => [],
],
'scout' => [
    'cost' => 0,
    'ratio' => 3,
    'level' => [],
    'need' => [
        'archer' => 15,
    ],
],
'thief' => [
    'cost' => 0,
    'ratio' => 3,
    'level' => [],
    'need' => [
        'soldier' => 6,
        'archer' => 6,
    ],
],
'healer' => [
    'cost' => 0,
    'ratio' => 3,
    'level' => [],
    'need' => [
        'soldier' => 10,
        'archer' => 8,
        'thief' => 8,
    ],
],
'warrior' => [
    'cost' => 0,
    'ratio' => 4,
    'level' => [],
    'need' => [
        'soldier' => 25,
    ],
],
'sniper' => [
    'cost' => 0,
    'ratio' => 4,
    'level' => [],
    'need' => [
        'archer' => 30,
        'scout' => 20,
    ],
],
'sorcerer' => [
    'cost' => 0,
    'ratio' => 4,
    'level' => [],
    'need' => [
        'healer' => 13,
    ],
],
'knight' => [
    'cost' => 0,
    'ratio' => 5,
    'level' => [],
    'need' => [
        'soldier' => 80,
        'warrior' => 32,
    ],
],
'ninja' => [
    'cost' => 0,
    'ratio' => 5,
    'level' => [],
    'need' => [
        'thief' => 20,
        'scout' => 16,
    ],
],
'alchemist' => [
    'cost' => 0,
    'ratio' => 5,
    'level' => [],
    'need' => [
        'healer' => 52,
    ],
],
'temple knight' => [
    'cost' => 0,
    'ratio' => 6,
    'level' => [],
    'need' => [
        'soldier' => 112,
        'warrior' => 54,
        'knight' => 25,
        'healer' => 45,
    ],
],
'cannoneer' => [
    'cost' => 0,
    'ratio' => 6,
    'level' => [],
    'need' => [
        'archer' => 50,
        'scout' => 50,
        'sniper' => 38,
    ],
],
'necromancer' => [
    'cost' => 0,
    'ratio' => 6,
    'level' => [],
    'need' => [
        'sorcerer' => 85,
    ],
],
'nightmare' => [
    'cost' => 0,
    'ratio' => 7,
    'level' => [],
    'need' => [
        'knight' => 54,
        'necromancer' => 60,
    ],
],
'assassin' => [
    'cost' => 0,
    'ratio' => 7,
    'level' => [],
    'need' => [
        'thief' => 95,
        'scout' => 85,
        'ninja' => 55,
    ],
],
'cleric' => [
    'cost' => 0,
    'ratio' => 7,
    'level' => [],
    'need' => [
        'healer' => 90,
        'alchemist' => 35,
    ],
],
'paladin' => [
    'cost' => 0,
    'ratio' => 8,
    'level' => [],
    'need' => [
        'temple knight' => 34,
        'cleric' => 45,
    ],
],
'meister' => [
    'cost' => 0,
    'ratio' => 8,
    'level' => [],
    'need' => [
        'archer' => 150,
        'scout' => 120,
        'sniper' => 100,
        'cannoneer' => 90,
    ],
],
'summoner' => [
    'cost' => 0,
    'ratio' => 8,
    'level' => [],
    'need' => [
        'sorcerer' => 115,
        'alchemist' => 72,
    ],
],
'holy knight' => [
    'cost' => 0,
    'ratio' => 9,
    'level' => [],
    'need' => [
        'paladin' => 42,
        'cleric' => 95,
    ],
],
'shadow knight' => [
    'cost' => 0,
    'ratio' => 9,
    'level' => [],
    'need' => [
        'nightmare' => 156,
    ],
],
'priest' => [
    'cost' => 0,
    'ratio' => 9,
    'level' => [],
    'need' => [
        'healer' => 125,
        'alchemist' => 86,
        'cleric' => 75,
    ],
],
'royalguard' => [
    'cost' => 0,
    'ratio' => 10,
    'level' => [],
    'need' => [
        'soldier' => 200,
        'warrior' => 150,
        'knight' => 150,
        'nightmare' => 150,
        'temple knight' => 150,
        'paladin' => 150,
        'holy knight' => 100,
        'shadow knight' => 100,
    ],
],
'wizard' => [
    'cost' => 0,
    'ratio' => 10,
    'level' => [],
    'need' => [
        'healer' => 200,
        'sorcerer' => 200,
        'alchemist' => 150,
        'cleric' => 120,
        'necromancer' => 120,
        'summoner' => 120,
        'priest' => 100,
    ],
],

];

function find_req($class) {
    $class = strtolower($class);
    if (!isset(A::$classes[$class])) throw new Exception($class . ' class not found');

    $init = A::$classes[$class];
    $result = $need = $init['need'];

    foreach ($need as $subclass => $level) {
        $subneed = find_req($subclass);
        $result = combine_req($result, $subneed);
    }

    return $result;
}

function combine_req($need, $add) {
    foreach ($add as $class => $level) {
        if (isset($need[$class])) {
            if ($need[$class] < $level) {
                $need[$class] = $level;
            }
        } else {
            $need[$class] = $level;
        }
    }
    return $need;
}

function sort_req($need) {
    uksort($need, function ($class1, $class2) {
        $data1 = A::$classes[$class1];
        $data2 = A::$classes[$class2];
        if ($data1['ratio'] == $data2['ratio']) {
            return 0;
        }
        return ($data1['ratio'] < $data2['ratio']) ? -1 : 1;
    });
    return $need;
}

if (!isset($argv[1])) {
    echo "php mof2.php [class name] // can put multiple class names\n";
    return;
}
$classes = array_slice($argv, 1);
$result = [];
foreach ($classes as $class) {
    $result = combine_req($result, find_req($class));
}
print_r(sort_req($result));
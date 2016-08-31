<?php
class A {
    public static $classes;
}
A::$classes = [
'soldier' => [
    'cost' => 100,
    'ratio' => 2,
    'level' => [130, 161, 191, 222, 252, 283, 313, 344, 374, 405], // 30.5
    'need' => [],
],
'archer' => [
    'cost' => 100,
    'ratio' => 2,
    'level' => [130, 161, 191, 222], // 30.5
    'need' => [],
],
'scout' => [
    'cost' => 150,
    'ratio' => 3,
    'level' => [195, 241, 287, 333, 378, 424, 470, 516, 561], // 46, 46, 46, 45
    'need' => [
        'archer' => 15,
    ],
],
'thief' => [
    'cost' => 150,
    'ratio' => 3,
    'level' => [195, 241, 287, 333], // 46, 46, 46, 45
    'need' => [
        'soldier' => 6,
        'archer' => 6,
    ],
],
'healer' => [
    'cost' => 150,
    'ratio' => 3,
    'level' => [195, 241, 287, 333], // 46, 46, 46, 45
    'need' => [
        'soldier' => 10,
        'archer' => 8,
        'thief' => 8,
    ],
],
'warrior' => [
    'cost' => 200,
    'ratio' => 4,
    'level' => [261, 322, 383, 444, 505, 566, 627, 688, 749, 810, 871, 932], // 61
    'need' => [
        'soldier' => 25,
    ],
],
'sniper' => [
    'cost' => 200,
    'ratio' => 4,
    'level' => [261, 322, 383, 444, 505, 566, 627, 688, 749, 810, 871, 932], // 61
    'need' => [
        'archer' => 30,
        'scout' => 20,
    ],
],
'sorcerer' => [
    'cost' => 250,
    'ratio' => 4,
    'level' => [261, 322, 383, 444, 505, 566, 627, 688, 749, 810, 871, 932], // 61
    'need' => [
        'healer' => 13,
    ],
],
'knight' => [
    'cost' => 300,
    'ratio' => 5,
    'level' => [376, 452, 528, 605, 681, 757, 833, 910, 986, 1062, 1138], // 76, 76, 77
    'need' => [
        'soldier' => 80,
        'warrior' => 32,
    ],
],
'ninja' => [
    'cost' => 300,
    'ratio' => 5,
    'level' => [376, 452, 528, 605, 681, 757, 833, 910, 986, 1062, 1138], // 76, 76, 77
    'need' => [
        'thief' => 20,
        'scout' => 16,
    ],
],
'alchemist' => [
    'cost' => 350,
    'ratio' => 5,
    'level' => [426, 502, 578, 655, 731, 807, 883, 960, 1036, 1112, 1188], // 76, 76, 77
    'need' => [
        'healer' => 52,
    ],
],
'temple knight' => [
    'cost' => 400,
    'ratio' => 6,
    'level' => [491, 583, 674, 766, 857, 949, 1040, 1132, 1223, 1315, 1406], // 92, 91
    'need' => [
        'soldier' => 112,
        'warrior' => 54,
        'knight' => 25,
        'healer' => 45,
    ],
],
'cannoneer' => [
    'cost' => 400,
    'ratio' => 6,
    'level' => [491, 583, 674, 766, 857, 949, 1040, 1132, 1223, 1315, 1406], // 92, 91
    'need' => [
        'archer' => 50,
        'scout' => 50,
        'sniper' => 38,
    ],
],
'necromancer' => [
    'cost' => 380,
    'ratio' => 6,
    'level' => [471, 563, 654, 746, 837, 929, 1020, 1112, 1203], // 92, 91
    'need' => [
        'sorcerer' => 85,
    ],
],
'nightmare' => [
    'cost' => 450,
    'ratio' => 7,
    'level' => [556, 663, 770, 877, 983, 1090, 1197, 1304, 1410, 1517], // 107, 107, 107, 106
    'need' => [
        'knight' => 54,
        'necromancer' => 60,
    ],
],
'assassin' => [
    'cost' => 460,
    'ratio' => 7,
    'level' => [566, 673, 780, 887, 993, 1100, 1207, 1314, 1420], // 107, 107, 107, 106
    'need' => [
        'thief' => 95,
        'scout' => 85,
        'ninja' => 55,
    ],
],
'cleric' => [
    'cost' => 420,
    'ratio' => 7,
    'level' => [526, 633, 740, 847, 953, 1060, 1167, 1274, 1380, 1487, 1594, 1701, 1807], // 107, 107, 107, 106
    'need' => [
        'healer' => 90,
        'alchemist' => 35,
    ],
],
'paladin' => [
    'cost' => 450,
    'ratio' => 8,
    'level' => [572, 694, 816, 938, 1060, 1182, 1304, 1426, 1548, 1670, 1792, 1914, 2036], // 122
    'need' => [
        'temple knight' => 34,
        'cleric' => 45,
    ],
],
'meister' => [
    'cost' => 480,
    'ratio' => 8,
    'level' => [602, 724, 846, 968, 1090, 1212, 1334, 1456, 1578, 1700, 1822], // 
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
    'cost' => 530,
    'ratio' => 9,
    'level' => [667, 804, 941, 1079, 1216, 1353, 1490, 1628, 1765, 1902, 2039, 2177, 2314], // 137, 137, 138
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
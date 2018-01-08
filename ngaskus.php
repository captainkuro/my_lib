<?php

function kirim($url, $posts) {
    // initialise the curl request
    $request = curl_init($url);

    // send a file
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt(
        $request,
        CURLOPT_POSTFIELDS,
        $posts);

    // output the response
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($request);

    // close the session
    curl_close($request);

    return $response;
}

function ambil_data($html) {
    preg_match('#<form action="https://s3-ap.*/form>#s', $html, $form_part);
    $form_part = $form_part[0];

    preg_match_all('#input [^/]*name="([^"]+)" [^/]*value="([^"]*)"#s', $form_part, $inputs);
    $result = [];
    foreach ($inputs[1] as $i => $key) {
        $value = $inputs[2][$i];
        $result[$key] = $value;
    }
    return $result;
}

$x = ambil_data(file_get_contents('/home/khandar-gdp/Downloads/coba1.html'));
$x['key'] = 'videos/55329/1024/300/sample2.mp4';
$x['success_action_redirect'] = '';
$x['Content-Type'] = 'video/mp4';
$x['file'] = '@'.realpath('/home/khandar-gdp/Downloads/sample2.mp4');
$y = kirim('https://s3-ap-southeast-1.amazonaws.com/upload.kaskus.co.id/', $x);
print_r($y);
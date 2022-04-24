<?php
    $rdt -> template = "home";
    $rdt -> page = (object) [];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => "https://www.reddit.com/.json"
    ]);

    $response = curl_exec($ch);
    $rdt -> page -> response =  $response;
    $rdtdata = json_decode($response);

    if (is_null($rdtdata -> data)) {
        die();
    }

    $rdt -> page -> postList = $rdtdata -> data -> children;
    $rdt -> page -> lastPost = end($rdt -> page -> postList);
<?php
    $root = $_SERVER["DOCUMENT_ROOT"];
    include($root . "/vendor/autoload.php");
    $pages = $root . "/pages";

    $twigLoader = new \Twig\Loader\FilesystemLoader($root . "/template");
    $twig = new \Twig\Environment($twigLoader);

    $rdt = (object) [];
    $rdt -> pid = getmypid();
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $rdt -> serverHost = exec("whoami");
    } else {
        $rdt -> serverHost = exec("whoami") . "@" . gethostname();
    }
    $rdt -> header = (object) [
        "srLinks" => [
            (object) [
                "label" => "popular",
                "url" => "/r/popular"
            ],
            (object) [
                "label" => "all",
                "url" => "/r/all"
            ],
            (object) [
                "label" => "random",
                "url" => "/r/random"
            ],
            (object) [
                "label" => "users",
                "url" => "/users"
            ]
        ]
    ];
    $rdt -> config = (object) [
        "old_favicon" => true
    ];
    $twig -> addGlobal("rdt", $rdt);
    $twig -> addFunction(new \Twig\TwigFunction("abbrNum", function($num) {
        if($num >= 10000) {
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('k', 'm', 'b', 't');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . '.' . ($x_array[1][0] ? $x_array[1][0] : 0);
            $x_display .= $x_parts[$x_count_parts - 1];
            return $x_display;
        } else {
            return $num;
        }
    }));

    $twig -> addFunction(new \Twig\TwigFunction("secToMin", function($secs) {
        if ($secs > 60) {
            $mins = floor($secs / 60);
            $newSecs = $secs - ($mins * 60);
            return $newSecs >= 10 ? $mins . ":" . $newSecs : $mins . ":0" . $secs;
        } else {
            return $secs >= 10 ? "0:" . $secs : "0:0" . $secs;
        }
    }));

    $twig -> addFunction(new \Twig\TwigFunction("timeAgo", function($time) {
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");
     
        $now = time();
        $difference = $now - $time;
        $tense = "ago";
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }
     
        $difference = round($difference);
     
        if($difference != 1) {
            $periods[$j] .= "s";
        }
     
        return "$difference $periods[$j] ago";
    }));

    function betterParseUrl($url) {
        $purl = parse_url($url);
        $response = (object) [];
        foreach(explode(' ', 'scheme host port user pass fragment') as $elm) {
            if (isset($purl[$elm])) {
                $response->{$elm} = $purl[$elm];
            }
        }
    
        if (isset($purl['path'])) {
            $temp = explode('/', $purl['path']);
            if ($temp[0] === '') {
                array_splice($temp, 0, 1);
            }
            $response->path = $temp;
        }
    
        if (isset($purl['query'])) {
            $response->query = explode('&', $purl['query']);
        }
    
        return $response;
    }
    
    $routerUrl = betterParseUrl($_SERVER['REQUEST_URI']);

    switch($routerUrl -> path[0]) {
        case "favicon.ico":
            if ($rdt -> config -> old_favicon) {
                header("Content-Type: image/x-icon");
                echo file_get_contents($root . "/favicons/old.ico");
                die();
            } else {
                header("Content-Type: image/png");
                echo file_get_contents($root . "/favicons/new.png");
                die();
            }
            break;
        case "":
            include($pages . "/home.php");
            break;
        case "r":
            include($pages . "/subreddit.php");
            break;
        default:
            http_response_code(404);
            $rdt -> template = "404";
            break;
    }

    if (isset($rdt -> template)) {
        echo $twig -> render($rdt -> template . ".twig");
    } else {
        http_response_code(404);
        echo $twig -> render("404.twig");
    }
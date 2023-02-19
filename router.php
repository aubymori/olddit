<?php
use \Rehike\ControllerV2\Router;

Router::funnel([
    "/favicon.ico",
    "/api/*"
]);

Router::get([
    "/" => "index"
]);
<?php
set_include_path($_SERVER["DOCUMENT_ROOT"]);

require "vendor/autoload.php";
require "modules/autoload.php";

use \Rehike\ControllerV2\Core as CV2;
use \Rehike\TemplateManager;

$app = (object) [];

TemplateManager::registerGlobalState($app);
CV2::registerStateVariable($app);

require "router.php";
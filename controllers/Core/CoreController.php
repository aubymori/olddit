<?php
namespace Olddit\Controller\Core;

use Rehike\TemplateManager;

abstract class CoreController {
    protected $app;
    public $template = "";
    public $useTemplate = true;
    public $contentType = "text/html";
    
    public function get(&$app, $request) {
        header("Content-Type: " . $this -> contentType);
        $this -> app = $app;

        if ($this -> useTemplate) echo TemplateManager::render([$app], $this -> template);
    }
}
<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;

use App\Risk\Action;
use App\Risk\TABLE_PREFIX;
use App\Risk\MODULE_LOGO;

class ActionController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'action'; 
        $table = new Action($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Actions';
        $template['javascript'] = $table->getJavascript();
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
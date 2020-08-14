<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;

use App\Risk\Indicator;
use App\Risk\TABLE_PREFIX;
use App\Risk\MODULE_LOGO;

class IndicatorController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'indicator'; 
        $table = new Indicator($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Indicators';
        $template['javascript'] = $table->getJavascript();
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
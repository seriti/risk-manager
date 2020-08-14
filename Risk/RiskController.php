<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;
use App\Risk\Risk;

class RiskController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'risk'; 
        $table = new Risk($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Objective Risks';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
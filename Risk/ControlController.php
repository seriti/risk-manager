<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;
use App\Risk\Control;

class ControlController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'control'; 
        $table = new Control($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Objective & Risk Controls';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
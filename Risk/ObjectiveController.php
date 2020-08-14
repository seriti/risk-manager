<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;
use App\Risk\Objective;

class ObjectiveController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'objective'; 
        $table = new Objective($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Objectives';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
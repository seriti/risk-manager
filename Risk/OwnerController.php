<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;
use App\Risk\Owner;

class OwnerController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'owner'; 
        $table = new Owner($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Objective & Action Owners';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
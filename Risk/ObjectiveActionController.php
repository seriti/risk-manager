<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;
use App\Risk\ActionLink;

class ObjectiveActionController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'action'; 
        $table = new ActionLink($this->container->mysql,$this->container,$table_name);

        $param = ['link'=>'OBJECTIVE'];
        $table->setup($param);
        $html = $table->processTable();
        
        $template['html'] = $html;
        //$template['title'] = MODULE_LOGO;
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}
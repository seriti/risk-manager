<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;
use App\Risk\Task;

class TaskController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $param = [];
        $task = new Task($this->container->mysql,$this->container,$param);

        $task->setup();
        $html = $task->processTasks();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Risk Tasks';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
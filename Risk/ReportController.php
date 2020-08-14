<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;

use App\Risk\Report;

class ReportController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $report = new Report($this->container->mysql,$this->container);
        
        $report->setup();
        $html = $report->process();

        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.' Reports';
        $template['javascript'] = $report->getJavascript();

        return $this->container->view->render($response,'admin.php',$template);
    }
}
<?php 
namespace App\Risk;

use Psr\Container\ContainerInterface;
use Seriti\Tools\BASE_URL;
use Seriti\Tools\SITE_NAME;

class Config
{
    
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        
        $module = $this->container->config->get('module','risk');
        //$ledger = $this->container->config->get('module','ledger');
        $menu = $this->container->menu;
        $cache = $this->container->cache;
        $db = $this->container->mysql;

        $user_specific = true;
        $cache->setCache('Risks',$user_specific);
        //$cache->eraseAll();
        
        define('TABLE_PREFIX',$module['table_prefix']);
        define('MODULE_ID','RISK');
        define('MODULE_LOGO','<img src="'.BASE_URL.'images/owl_icon.png"> ');
        define('MODULE_PAGE',URL_CLEAN_LAST);
        
        define('OWNER_TYPE',['ACCOUNT'=>'Accountable',
                             'RESPONSE'=>'Responsible',
                             'INFORM'=>'Informed']);

        define('LINK_TO',['OBJECTIVE'=>'Objective',
                          'RISK'=>'Risk',
                          'CONTROL'=>'Control']);

        define('REPEAT_FREQ',['ONCE'=>'Once off',
                              'DAY'=>'Daily',
                              'WEEK'=>'Weekly',
                              'MONTH'=>'Monthly',
                              'QUARTER'=>'Quarterly',
                              'SEMI_ANNUAL'=>'Semi-annualy',
                              'ANNUAL'=>'Annualy']);

        define('IMPACT_LIST',[1=>'1 - lowest',
                              2=>'2 - very Low',
                              3=>'3 - low',
                              4=>'4 - medium',
                              5=>'5 - medium/high', 
                              6=>'6 - high', 
                              7=>'7 - very high',
                              8=>'8 - Severe',
                              9=>'9 - very severe',
                              10=>'10 - maximum',
                             ]);

        define('LIKELY_LIST',[1=>'1 - very unlikely',
                              2=>'2 - unlikely',
                              3=>'3 - low likelyhood',
                              4=>'4 - medium likelyhood',
                              5=>'5 - likely', 
                              6=>'6 - very likely', 
                              7=>'7 - almost certain',
                              8=>'8 - certain',
                              9=>'9 - very certain',
                              10=>'10 - absolutely certain',
                             ]);

        define('UNIT_TYPE',['INTEGER'=>'Whole number',
                            'PERCENT'=>'Percentage %',
                            'DECIMAL'=>'Number']);
        
        $submenu_html = $menu->buildNav($module['route_list'],MODULE_PAGE);
        $this->container->view->addAttribute('sub_menu',$submenu_html);
       
        $response = $next($request, $response);
        
        return $response;
    }
}
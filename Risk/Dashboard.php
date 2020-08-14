<?php
namespace App\Risk;

use Seriti\Tools\Dashboard AS DashboardTool;

class Dashboard extends DashboardTool
{
     

    //configure
    public function setup($param = []) 
    {
        $this->col_count = 2;  

        $login_user = $this->getContainer('user'); 

        //(block_id,col,row,title)
        $this->addBlock('ADD',1,1,'Capture new data');
        $this->addItem('ADD','Add a new Objective',['link'=>"objective?mode=add"]);
        $this->addItem('ADD','Add a new Action',['link'=>"action?mode=add"]);
        $this->addItem('ADD','Add a new Control',['link'=>"control?mode=add"]);
        $this->addItem('ADD','Add a new Owner',['link'=>"owner?mode=add"]);
        
        if($login_user->getAccessLevel() === 'GOD') {
            $this->addBlock('CONFIG',1,3,'Module Configuration');
            $this->addItem('CONFIG','Setup Database',['link'=>'setup_data','icon'=>'setup']);
        }    
        
    }

}

?>
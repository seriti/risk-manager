<?php
namespace App\Risk;

use Seriti\Tools\Table;

use App\Risk\TABLE_PREFIX;

class ControlLink extends Table 
{
    //configure
    public function setup($param = []) 
    {
        if(!isset($param['link'])) $param['link'] = 'RISK';

        switch($param['link']) {
            case 'OBJECTIVE': 
                $title = 'Controls for Objective';
                $link_to = 'OBJECTIVE';
                $link_table = TABLE_PREFIX.'objective';
                $link_key = 'objective_id';
                break;
            case 'RISK' : 
                $title = 'Controls for Risk';
                $link_to = 'RISK';
                $link_table = TABLE_PREFIX.'risk';
                $link_key = 'risk_id';
                break;

        }

        $parent_param = ['row_name'=>'control','col_label'=>'name','pop_up'=>true];
        parent::setup($parent_param);       
        
        $this->setupMaster(array('table'=>$link_table,'key'=>$link_key,'child_col'=>'link_to_id','label'=>'name', 
                                'show_sql'=>'SELECT CONCAT("'.$title.': ",name) FROM '.$link_table.' WHERE '.$link_key.' = "{KEY_VAL}" '));  

        $this->addColFixed(['id'=>'link_to','value'=>$link_to]);

        $this->addTableCol(array('id'=>'link_id','type'=>'INTEGER','title'=>'Link ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'control_id','type'=>'INTEGER','title'=>'Linked Control','join'=>'name FROM '.TABLE_PREFIX.'control WHERE control_id'));
        $this->addTableCol(array('id'=>'mitigate_pct','type'=>'INTEGER','title'=>'Mitigation %'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));
             
        $this->addSql('WHERE','T.link_to = "'.$link_to.'" ');       
        $this->addSql('JOIN','LEFT JOIN '.TABLE_PREFIX.'control AS C ON(T.control_id = C.control_id)');

        $this->addAction(array('type'=>'check_box','text'=>''));
        //$this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));

        $this->addSelect('control_id','SELECT control_id,name FROM '.TABLE_PREFIX.'control ORDER BY name');
        $this->addSelect('status','(SELECT "OK") UNION (SELECT "INACTIVE")');
        
        $this->addSearch(array('link_id'),array('rows'=>2));
        $this->addSearchXtra('C.name','control name');
    }

    protected function modifyRowValue($col_id,$data,&$value) {
        
    }    
}
?>

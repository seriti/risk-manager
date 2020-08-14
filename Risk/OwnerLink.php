<?php
namespace App\Risk;

use Seriti\Tools\Table;

use App\Risk\TABLE_PREFIX;

class OwnerLink extends Table 
{
    //configure
    public function setup($param = []) 
    {
        if(!isset($param['link'])) $param['link'] = 'ACTION';

        switch($param['link']) {
            case 'ACTION' : 
                $title = 'Owners for Action';
                $link_to = 'ACTION';
                $link_table = TABLE_PREFIX.'action';
                $link_key = 'action_id';
                break;
            case 'CONTROL' : 
                $title = 'Owners for Control';
                $link_to = 'CONTROL';
                $link_table = TABLE_PREFIX.'control';
                $link_key = 'control_id';
                break;
            case 'INDICATOR' : 
                $title = 'Owners for Indicator';
                $link_to = 'INDICATOR';
                $link_table = TABLE_PREFIX.'indicator';
                $link_key = 'indicator_id';
                break;    

            case 'OBJECTIVE' : 
                $title = 'Owners for Objective';
                $link_to = 'OBJECTIVE';
                $link_table = TABLE_PREFIX.'objective';
                $link_key = 'objective_id';
                break;
            case 'RISK' : 
                $title = 'Owners for Risk';
                $link_to = 'RISK';
                $link_table = TABLE_PREFIX.'risk';
                $link_key = 'risk_id';
                break;

        }

        $parent_param = ['row_name'=>'owner','col_label'=>'name','pop_up'=>true];
        parent::setup($parent_param);       
        
        $this->setupMaster(array('table'=>$link_table,'key'=>$link_key,'child_col'=>'link_to_id','label'=>'name', 
                                'show_sql'=>'SELECT CONCAT("'.$title.': ",name) FROM '.$link_table.' WHERE '.$link_key.' = "{KEY_VAL}" '));  

        $this->addColFixed(['id'=>'link_to','value'=>$link_to]);

        $this->addTableCol(array('id'=>'link_id','type'=>'INTEGER','title'=>'Link ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'owner_id','type'=>'INTEGER','title'=>'Linked Owner','join'=>'name FROM '.TABLE_PREFIX.'owner WHERE owner_id'));
        $this->addTableCol(array('id'=>'link_type','type'=>'STRING','title'=>'Owner type'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));
             
        $this->addSql('WHERE','T.link_to = "'.$link_to.'" ');       
        $this->addSql('JOIN','LEFT JOIN '.TABLE_PREFIX.'owner AS O ON(T.owner_id = O.owner_id)');

        $this->addAction(array('type'=>'check_box','text'=>''));
        //$this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));

        $this->addSelect('owner_id','SELECT owner_id,name FROM '.TABLE_PREFIX.'owner ORDER BY name');
        $this->addSelect('status','(SELECT "OK") UNION (SELECT "INACTIVE")');
        $this->addSelect('link_type',['list'=>OWNER_TYPE]);

        $this->addSearch(array('link_id'),array('rows'=>2));
        $this->addSearchXtra('O.name','Owner name');
        $this->addSearchXtra('O.email','Owner email');
    }

    protected function modifyRowValue($col_id,$data,&$value) {
        if($col_id === 'link_type') {
            if(isset(OWNER_TYPE[$value])) {
                $value = OWNER_TYPE[$value];
            } 
        }
    }    
}
?>

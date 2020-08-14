<?php 
namespace App\Risk;

use Seriti\Tools\Table;

use App\Risk\TABLE_PREFIX;
use App\Risk\Helpers;


class Control extends Table 
{
    
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Control','col_label'=>'name'];
        parent::setup($param);        

        //$this->addForeignKey(array('table'=>TABLE_PREFIX.'action','col_id'=>'_id','message'=>'Actions'));
        
        $this->addTableCol(array('id'=>'control_id','type'=>'INTEGER','title'=>'Control ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'description','type'=>'TEXT','title'=>'Description','required'=>false));
        $this->addTableCol(array('id'=>'repeat_freq','type'=>'STRING','title'=>'Frequency'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));
        $this->addAction(array('type'=>'popup','text'=>'Owners','url'=>'control_owner','mode'=>'view','width'=>600,'height'=>600));

        //NB: CANNOT HAVE ANY ACCOUNT_ID FIELDS IN SEARCH OPTIONS
        $this->addSearch(array('name','email','status'),array('rows'=>2));
        
        $this->addSelect('repeat_freq',['list'=>REPEAT_FREQ]);  
        $this->addSelect('status','(SELECT "OK") UNION (SELECT "INACTIVE")');
    }
     
}
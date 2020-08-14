<?php 
namespace App\Risk;

use Seriti\Tools\Table;

use App\Risk\TABLE_PREFIX;
use App\Risk\Helpers;


class Owner extends Table 
{
    
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Owner','col_label'=>'name'];
        parent::setup($param);        

        //$this->addForeignKey(array('table'=>TABLE_PREFIX.'action','col_id'=>'_id','message'=>'Actions'));
        
        $this->addTableCol(array('id'=>'owner_id','type'=>'INTEGER','title'=>'Owner ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Name'));
        $this->addTableCol(array('id'=>'email','type'=>'EMAIL','title'=>'Email address'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));

        //NB: CANNOT HAVE ANY ACCOUNT_ID FIELDS IN SEARCH OPTIONS
        $this->addSearch(array('name','email','status'),array('rows'=>2));
          
        $this->addSelect('status','(SELECT "OK") UNION (SELECT "INACTIVE")');
    }
     
}
<?php 
namespace App\Risk;

use Seriti\Tools\Table;

use App\Risk\TABLE_PREFIX;
use App\Risk\Helpers;


class Risk extends Table 
{
    
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Risk','col_label'=>'name'];
        parent::setup($param);        

        //$this->addForeignKey(array('table'=>TABLE_PREFIX.'action','col_id'=>'_id','message'=>'Actions'));
        
        $this->addTableCol(array('id'=>'risk_id','type'=>'INTEGER','title'=>'Risk ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'objective_id','type'=>'INTEGER','title'=>'Objective','join'=>'name FROM '.TABLE_PREFIX.'objective WHERE objective_id'));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Risk name'));
        $this->addTableCol(array('id'=>'description','type'=>'TEXT','title'=>'Description','size'=>40,'required'=>false));
        $this->addTableCol(array('id'=>'impact_id','type'=>'INTEGER','title'=>'Impact severity','new'=>1));
        $this->addTableCol(array('id'=>'likely_id','type'=>'INTEGER','title'=>'Likelyhood','new'=>1));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));
        $this->addAction(array('type'=>'popup','text'=>'Owners','url'=>'risk_owner','mode'=>'view','width'=>600,'height'=>600));
        $this->addAction(array('type'=>'popup','text'=>'Controls','url'=>'risk_control','mode'=>'view','width'=>600,'height'=>600));

        //NB: CANNOT HAVE ANY ACCOUNT_ID FIELDS IN SEARCH OPTIONS
        $this->addSearch(array('name','description','objective_id','status'),array('rows'=>2));
          
        $this->addSelect('status','(SELECT "OK") UNION (SELECT "INACTIVE")');
        $this->addSelect('objective_id','SELECT objective_id,name FROM '.TABLE_PREFIX.'objective ORDER BY name'); 
        $this->addSelect('impact_id',['list'=>IMPACT_LIST]); 
        $this->addSelect('likely_id',['list'=>LIKELY_LIST]); 
    }

    protected function modifyRowValue($col_id,$data,&$value) {
        if($col_id === 'impact_id') {
            if(isset(IMPACT_LIST[$value])) {
                $value = IMPACT_LIST[$value];
            } 
        }

        if($col_id === 'likely_id') {
            if(isset(LIKELY_LIST[$value])) {
                $value = LIKELY_LIST[$value];
            } 
        }
    
    } 
     
}
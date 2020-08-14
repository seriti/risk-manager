<?php 
namespace App\Risk;

use Seriti\Tools\Table;

use App\Risk\TABLE_PREFIX;
use App\Risk\IMPACT_LIST;
use App\Risk\LIKELY_LIST;

use App\Asset\Helpers;


class Objective extends Table 
{
    
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Objective','col_label'=>'name'];
        parent::setup($param);        

        $this->addForeignKey(array('table'=>TABLE_PREFIX.'action','col_id'=>'objective_id','message'=>'Actions'));
        $this->addForeignKey(array('table'=>TABLE_PREFIX.'risk','col_id'=>'objective_id','message'=>'Price'));
        
        $this->addTableCol(array('id'=>'objective_id','type'=>'INTEGER','title'=>'Objective ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'unit_id','type'=>'INTEGER','title'=>'Organisation Unit','join'=>'title FROM '.TABLE_PREFIX.'unit WHERE id'));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Objective name'));
        $this->addTableCol(array('id'=>'description','type'=>'TEXT','title'=>'Description','size'=>40,'required'=>false));
        $this->addTableCol(array('id'=>'impact_id','type'=>'INTEGER','title'=>'Significance','new'=>1));
        $this->addTableCol(array('id'=>'likely_id','type'=>'INTEGER','title'=>'Certainty','new'=>1));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));

        $this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));
        $this->addAction(array('type'=>'popup','text'=>'Owners','url'=>'objective_owner','mode'=>'view','width'=>600,'height'=>600)); 
        $this->addAction(array('type'=>'popup','text'=>'Controls','url'=>'objective_control','mode'=>'view','width'=>600,'height'=>600));

        //NB: CANNOT HAVE ANY ACCOUNT_ID FIELDS IN SEARCH OPTIONS
        $this->addSearch(array('name','description','unit_id','impact_id','likely_id','status'),array('rows'=>2));
          
        $this->addSelect('status','(SELECT "OK") UNION (SELECT "INACTIVE")');
        $this->addSelect('impact_id',['list'=>IMPACT_LIST]); 
        $this->addSelect('likely_id',['list'=>LIKELY_LIST]); 

        $sql_unit = 'SELECT id,CONCAT(IF(level > 1,REPEAT("--",level - 1),""),title) FROM '.TABLE_PREFIX.'unit  ORDER BY rank';
        $this->addSelect('unit_id',$sql_unit);
        
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
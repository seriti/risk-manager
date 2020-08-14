<?php
namespace App\Risk;

use Seriti\Tools\Table;

use App\Risk\TABLE_PREFIX;

class Indicator extends Table 
{
    //configure
    public function setup($param = []) 
    {
        $param = ['row_name'=>'Indicator','col_label'=>'name'];
        parent::setup($param); 

        $start_default = date('Y-m-d');
        $end_default = date('Y-m-d',mktime(0,0,0,date('m')+12,date('j'),date('Y')));

        $this->addTableCol(array('id'=>'indicator_id','type'=>'INTEGER','title'=>'Indicator ID','key'=>true,'key_auto'=>true,'list'=>true));
        $this->addTableCol(array('id'=>'link_to','type'=>'STRING','title'=>'Link To','onchange'=>'linkToChange()'));
        $this->addTableCol(array('id'=>'link_to_id','type'=>'INTEGER','title'=>'Link to Item'));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Indicator name'));
        $this->addTableCol(array('id'=>'description','type'=>'TEXT','title'=>'Description','required'=>false));
        $this->addTableCol(array('id'=>'repeat_freq','type'=>'STRING','title'=>'Frequency','new'=>'MONTH'));
        $this->addTableCol(array('id'=>'date_start','type'=>'DATE','title'=>'Date start','new'=>$start_default));
        $this->addTableCol(array('id'=>'date_end','type'=>'DATE','title'=>'Date end','new'=>$end_default));
        $this->addTableCol(array('id'=>'target_value','type'=>'DECIMAL','title'=>'Target value','new'=>'100'));
        $this->addTableCol(array('id'=>'target_units','type'=>'STRING','title'=>'Target units','new'=>'PERCENT'));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status'));
            
        
        //$this->addAction(array('type'=>'check_box','text'=>''));
        $this->addAction(array('type'=>'edit','text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R'));
        $this->addAction(array('type'=>'popup','text'=>'Owners','url'=>'indicator_owner','mode'=>'view','width'=>600,'height'=>600));
        
        $this->addSelect('repeat_freq',['list'=>REPEAT_FREQ]);
        $this->addSelect('link_to',['list'=>LINK_TO]);
        $this->addSelect('link_to_id',['list'=>[],'list_assoc'=>true]);
        $this->addSelect('target_units',['list'=>UNIT_TYPE]);
        $this->addSelect('status','(SELECT "OK") UNION (SELECT "INACTIVE")');
        
        $this->addSearch(array('action_id','link_to','name','description','repeat_freq',
                               'date_start','date_end','target_units','target_value','status'),array('rows'=>2));
        
    }
    
    protected function modifyRowValue($col_id,$data,&$value) {
        if($col_id === 'link_to_id') {
            $sql = '';
            switch($data['link_to']) {
                case 'OBJECTIVE':
                    $sql = 'SELECT name FROM '.TABLE_PREFIX.'objective WHERE objective_id ';
                    break;
                case 'RISK':
                    $sql = 'SELECT name FROM '.TABLE_PREFIX.'risk WHERE risk_id ';
                    break;
                case 'CONTROL':
                    $sql = 'SELECT name FROM '.TABLE_PREFIX.'control WHERE control_id ';
                    break;
            } 

            if($sql !== '') {
                $sql .= ' = "'.$this->db->escapeSql($value).'" ';
                $name = $this->db->readSqlValue($sql,0);
                if($name !== 0) $value = $name;
            }


        }

        if($col_id === 'repeat_freq') {
            if(isset(REPEAT_FREQ[$value])) {
                $value = REPEAT_FREQ[$value];
            } 
        }

        if($col_id === 'target_units') {
            if(isset(UNIT_TYPE[$value])) {
                $value = UNIT_TYPE[$value];
            } 
        }
    
    } 
    
    public function getJavascript()
    {
        $js = "
        <script type='text/javascript'>
        $(document).ready(function() {
            if(form = document.getElementById('update_form')) {
                linkToChange();
            }
        });

        function linkToChange() {
            var form = document.getElementById('update_form');
            var link_to = form.link_to.value;
            var link_to_id = form.link_to_id.value;
                      
            var param = 'link_to='+link_to;
            //alert('PARAM:'+param);
            xhr('ajax?mode=ITEM_LINK',param,showItemList,link_to_id);
              
        } 

        function showItemList(str,link_to_id) {
            //alert(str);
            if(str === 'ERROR') {
                alert('NO Link to items found!');
            } else {  
                var links = $.parseJSON(str);
                var sel = '';
                //use jquery to reset cols select list
                $('#link_to_id option').remove();
                $.each(links, function(i,item){
                    // Create and append the new options into the select list
                    if(i == link_to_id) sel = 'SELECTED'; else sel = '';
                    $('#link_to_id').append('<option value='+i+' '+sel+'>'+item+'</option>');
                });
            }    
        }
        </script>";

        return $js;

    }


}
?>

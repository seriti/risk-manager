<?php 
namespace App\Risk;

use App\Risk\Helpers;
use App\Risk\EMAIL_LIMIT_HOUR;

use Seriti\Tools\CURRENCY_ID;
use Seriti\Tools\Form;
use Seriti\Tools\Task as SeritiTask;

class Task extends SeritiTask
{
    protected $limit_send = false;
    protected $batch_no = 10;
    //NB this is only displayed here, also needs to be set in Ajax Class
    protected $batch_send_no = 50;

    public function setup()
    {
        $this->addBlock('SETUP',1,1,'Risk setup');
        $this->addTask('SETUP','SETUP_UNITS','Setup business units');
        $this->addTask('SETUP','SETUP_OWNERS','Setup risk owners');

        $this->addBlock('QUEUE',2,1,'Risk Message queue');
        //$this->addTask('QUEUE','QUEUE_ACTIONS','Queue action emails');
        //$this->addTask('QUEUE','QUEUE_INDICATORS','Queue indicator emails');

        //check within emails per hour limit
        if(defined('EMAIL_LIMIT_HOUR')) {
            $sql = 'SELECT COUNT(*) FROM '.TABLE_QUEUE.' '.
                   'WHERE process_id LIKE "RISK_%" AND process_complete = 1  AND TIMESTAMPDIFF(MINUTE,date_process,NOW()) < 60 ';
            $emails_last_hour = $this->db->readSqlValue($sql);
            
            if($emails_last_hour >= EMAIL_LIMIT_HOUR) {
                $this->limit_send = true;
                $this->addMessage('You have exceeded your emails per hour limit('.EMAIL_LIMIT_HOUR.') you will have to wait until you can send more.'); 
            }    
        }
        

        $sql = 'SELECT process_id,COUNT(*) FROM '.TABLE_QUEUE.' '.
               'WHERE process_id LIKE "RISK_%" AND process_complete = 0 AND process_status <> "ERROR" '.
               'GROUP BY process_id';
        $message_queue = $this->db->readSqlList($sql);
        if($message_queue != 0) {
            foreach($message_queue as $process_id=>$count) {
                if(strpos($process_id,'ACTION') !== false) {
                    $param = [];
                    $param['ajax'] = true;
                    $param['url'] = 'ajax?mode=RISK_ACTION&send='.$this->batch_send_no;
                    $param['flag_complete'] = 'DONE';
                    $param['div_progress'] = 'div_ajax';
                    $param['run_limit'] = $this->batch_no;
                    $this->addTask('QUEUE','ACTION_EMAIL','Process <b>'.$count.'</b> Risk ACTIONS',$param);
                    $this->addTask('QUEUE','ACTION_CLEAR','Remove <b>'.$count.'</b> Risk ACTIONS',$param);
                }

                if(strpos($process_id,'INDICATOR') !== false) {
                    $param = [];
                    $param['ajax'] = true;
                    $param['url'] = 'ajax?mode=RISK_INDICATOR&send='.$this->batch_send_no;
                    $param['flag_complete'] = 'DONE';
                    $param['div_progress'] = 'div_ajax';
                    $param['run_limit'] = $this->batch_no;
                    $this->addTask('QUEUE','INDICATOR_EMAIL','Process <b>'.$count.'</b> Risk INDICATORS',$param);
                    $this->addTask('QUEUE','INDICATOR_CLEAR','Remove <b>'.$count.'</b> Risk INDICATORS',$param);
                }
            }
        } else {
            $this->addMessage('NO unprocessed risk actions or indicators found in queue.'); 
        }
    }

    public function processTask($id,$param = []) {
        $error = '';
        $message = '';
        $n = 0;
        
        
        if($id === 'SETUP_UNITS') {
            $location = 'unit';
            header('location: '.$location);
            exit;
        }

        if($id === 'SETUP_OWNERS') {
            $location = 'owner';
            header('location: '.$location);
            exit;
        }

        
           
    }
}
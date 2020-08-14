<?php
namespace App\Risk;

use Psr\Container\ContainerInterface;

use Seriti\Tools\Secure;
use Seriti\Tools\Audit;
use Seriti\Tools\TABLE_QUEUE;

use App\Risk\Helpers;
use App\Risk\MessageQueue;


class Ajax
{
    protected $container;
    protected $db;
    protected $user_id;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $this->container->mysql;

        $this->user_id = $this->container->user->getId();
    }


    public function __invoke($request, $response, $args)
    {
        $mode = '';
        $output = '';

        if(isset($_GET['mode'])) $mode = Secure::clean('basic',$_GET['mode']);

        if($mode === 'EMAIL') {
            $message_id = Secure::clean('integer',$_GET['message']);
            $send_no = Secure::clean('integer',$_GET['send']);
            $output = $this->processEmailQueue($message_id,$send_no);

            Helpers::updateMessageInfo($this->db,$message_id);
        } 

        if($mode === 'ITEM_LINK') {  
            $output = $this->getLinkedToList(); 
        }

        return $output;
    }

    protected function processEmailQueue($message_id,$send_no)
    {
        $error = '';
        $html = '';

        //using generic system queue table
        $queue = new MessageQueue($this->db,$this->container,TABLE_QUEUE);
        $queue->setup();
        $queue->setupMailer($message_id,$error);

        if($error !== '') return $error;
        
        $process_id = 'RISK_MSG'.$message_id;
        $param = ['max_items'=>$send_no];
        $queue->processQueue($process_id,$param); 

        $queue->closeMailer();

        if($queue->getQueueInfo('COMPLETE') === true) {
            //NB: "DONE" must be first 4 chars to indicate process completed to javascript
            $html .= 'DONE: All messages in Risk Queue have been processed';
            $audit_str = 'Risk tasks: All email messages in queue processed!';
            $action_id = $process_id.'_DONE';
            Audit::action($this->db,$this->user_id,$action_id,$audit_str);
        } else { 
            $html .= $queue->getQueueInfo('MESSAGES');
        } 

        return $html;    
    }

    protected function getLinkedToList()
    {
        $output = '';

        $link_to = Secure::clean('basic',$_POST['link_to']);
        
        switch($link_to) {
            case 'OBJECTIVE':
                $sql = 'SELECT objective_id,name FROM '.TABLE_PREFIX.'objective ORDER BY name';
                break;
            case 'RISK':
                $sql = 'SELECT risk_id,name FROM '.TABLE_PREFIX.'risk ORDER BY name';
                break;
            case 'CONTROL':
                $sql = 'SELECT control_id,name FROM '.TABLE_PREFIX.'control ORDER BY name';
                break;
        }

        //get list of linked action entities
        $links = $this->db->readSqlList($sql);
                    
        if($links == 0) {
            $output = 'ERROR';
        } else {
            $output = json_encode($links);    
        }    

        return $output;

    }
    
}
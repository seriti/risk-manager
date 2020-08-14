<?php 
namespace App\Risk;

use Exception;
use Seriti\Tools\Csv;
use Seriti\Tools\Date;
use Seriti\Tools\Crypt;
use Seriti\Tools\Queue;
use Seriti\Tools\Audit;
use Seriti\Tools\Upload;
use Seriti\Tools\TABLE_QUEUE;

//use App\Risk\TemplateImage;

use Psr\Container\ContainerInterface;


//static functions for saveme module
class Helpers {

    //NB this is generally called oustide admin interface so make sure constant TABLE_PREFIX is defined correctly
    //use to deny/cancel ownership
    public static function unsubscribeContact($db,$guid,&$error) 
    {
        $error = '';

        if($guid === '') {
            $error = 'No contact identifier given.';
        } else {
            $sql = 'SELECT contact_id,name,surname,email,status '.
                   'FROM '.TABLE_PREFIX.'contact WHERE guid = "'.$db->escapeSql($guid).'" ';
            $contact = $db->readSqlRecord($sql); 
            if($contact == 0) {
                $error = 'Could not recognise unsubscribe link identifier['.$guid.'].';
            } else {
                if($contact['status'] !== 'HIDE') {
                    $sql = 'UPDATE '.TABLE_PREFIX.'contact SET status = "HIDE" '.
                           'WHERE contact_id = "'.$contact['contact_id'].'" '; 
                    $db->executeSql($sql,$error_tmp);
                    if($error_tmp !== '') $error .= 'Could not update contact status.';
                }
            }
        }

        if($error === '') return $contact; else return false;    
    }

    //modify this to update action progress_pct  ...etc
    public static function updateMessageInfo($db,$message_id) 
    {
        $error = '';
        $info_str = '';

        $process_id = 'CONTACT_MSG'.$message_id;
        $sql = 'SELECT process_status,COUNT(*) FROM '.TABLE_QUEUE.' '.
               'WHERE process_id = "'.$db->escapeSql($process_id).'" '.
               'GROUP BY process_status ';
        $info = $db->readSqlList($sql);
        if($info != 0 ) {
            if($info['NEW'] > 0) $info_str .= 'Message awaiting processing for '.$info['NEW']." contacts\r\n";
            if($info['DONE'] > 0) $info_str .= 'Message successfully sent to '.$info['DONE']." contacts \r\n";
            if($info['ERROR'] > 0) $info_str .= 'Messages not sent due to errors for '.$info['ERROR']." contacts\r\n";
        } else {
            $info_str .= 'Message not added to queue yet!';
        }

        $sql = 'UPDATE '.TABLE_PREFIX.'message SET info = "'.$db->escapeSql($info_str).'" '.
               'WHERE message_id = "'.$db->escapeSql($message_id).'" ';
        $db->executeSql($sql,$error);

        if($error === '') return true; else return false;
    }  


    public static function getLinkedOwners($db,$link_to,$link_to_id)
    {
        $table_owner = TABLE_PREFIX.'owner';
        $table_link = TABLE_PREFIX.'owner_link';

        $sql = 'SELECT O.owner_id,O.name '.
               'FROM '.$table_link.' AS L '.
               'JOIN '.$table_owner.' AS O ON(L.owner_id = O.owner_id) '.
               'WHERE L.link_to = "'.$link_to.'" AND L.link_to_id = "'.$db->escapeSql($link_to_id).'" AND O.status <> "INACTIVE" ';
        $owners = $db->readSqlList($sql);

        return $owners; 
    }

    public static function getAction($db,$action_id)
    {
        $date_now = date('Y-m-d');

        $sql = 'SELECT link_to,link_to_id,name,description,repeat_freq,date_start,date_end,status '.
               'FROM '.TABLE_PREFIX.'action WHERE action_id = "'.$db->escapeSql($action_id).'" ';
        $action = $db->readSqlRecord($sql);
        if($action != 0) {
            $date_start = Date::getDate($action['date_start']);
            $date_end = Date::getDate($action['date_end']);
            if($action['status'] === 'INACTIVE') {
                $action['active'] = false;
                $action['valid'] = false;
            } else {
                $action['active'] = true;
                $action['valid'] = self::checkValidRepeatDate($date_now,$action['date_start'],$action['date_end'],$action['repeat_freq']);
            }
        }

        return $action;
    }

    //dates are assumed YYYY-MM-DD format
    public static function checkValidRepeatDate($date,$date_start,$date_end,$repeat_freq)
    {
        $valid = false;

        $date = Date::getDate($date);
        $date_start = Date::getDate($date_start);
        $date_end = Date::getDate($date_end);

        $month_count = ($date['year'] * 12 + $date['mon']) - ($date_start['year'] * 12 + $date_start['mon']);

        if($date[0] >= $date_start[0] and $date[0] <= $date_end[0]) {  
            switch($repeat_freq) {
                case 'DAY': {
                    $valid = true;
                    break;
                }    
                case 'WEEK': {
                    if($date['wday'] === $date_start['wday']) $valid = true;
                    break;
                } 
                case 'MONTH': {
                    if($date['mday'] === $date_start['mday']) $valid = true;
                    break;
                }  
                case 'QUARTER': {
                    if(fmod($month_count,3) === 0 and $date['mday'] === $date_start['mday']) $valid = true;
                    break;
                }
                case 'SEMI_ANNUAL': {
                    if(fmod($month_count,6) === 0 and $date['mday'] === $date_start['mday']) $valid = true;
                    break;
                }
                case 'ANNUAL': {
                    if(fmod($month_count,12) === 0 and $date['mday'] === $date_start['mday']) $valid = true;
                    break;
                }
            }
        }    

        return $valid;
    }

    public static function addActionQueue($db,ContainerInterface $container,$action_id,&$error) 
    {
        $error = '';
        $output = '';

        


        $owners = self::getLinkedOwners($db,'ACTION',$action_id);
        
        if($owners == 0) {
            $error = 'No owners found for Action ID['.$action_id.']';
            $output = false;
        } else {
            $count = count($owners);

            $queue = new Queue($db,$container,TABLE_QUEUE);
            $queue->setup();
            
            //all actions processed together
            $process_id = 'RISK_ACTION';

            //don't want to clog up audit trail 
            $db->disableAudit();
            foreach($owners as $owner_id => $name) {
                //item_key prevents same owner receiving a message twice
                $item_key = 'ACTION'.$action_id.'-ID'.$owner_id;
                $item_data = ['action_id'=>$action_id,'owner_id'=>$owner_id,'name'=>$name];
                $queue->addItem($process_id,$item_key,$item_data,'NEW');
            }
            $db->enableAudit();

            $exist_no = $queue->getQueueInfo('EXIST');
            $add_no = $queue->getQueueInfo('ADDED');

            $output = "Action[$action_id]: Added $add_no owners to queue.";
            if($exist_no != 0) $output .= " $exist_no owners are allready in action queue or have been processed before.";

        } 

        return $output;
    }

    public static function addIndicatorQueue($db,ContainerInterface $container,$indicator_id,&$error) 
    {
        $error = '';
        $output = '';

        $owners = self::getLinkedOwners($db,'INDICATOR',$indicator_id);
        
        if($owners == 0) {
            $error = 'No owners found for Indicator ID['.$indicator_id.']';
            $output = false;
        } else {
            $count = count($owners);

            $queue = new Queue($db,$container,TABLE_QUEUE);
            $queue->setup();
            
            //all indicators processed together
            $process_id = 'RISK_INDICATOR';

            //don't want to clog up audit trail 
            $db->disableAudit();
            foreach($owners as $owner_id => $name) {
                //item_key prevents same owner receiving a message twice
                $item_key = 'INDICATOR'.$indicator_id.'-ID'.$owner_id;
                $item_data = ['indicator_id'=>$indicator_id,'owner_id'=>$owner_id,'name'=>$name];
                $queue->addItem($process_id,$item_key,$item_data,'NEW');
            }
            $db->enableAudit();

            $exist_no = $queue->getQueueInfo('EXIST');
            $add_no = $queue->getQueueInfo('ADDED');

            $output = "Indicator[$indicator_id]: Added $add_no owners to queue.";
            if($exist_no != 0) $output .= " $exist_no owners are allready in indicator queue or have been processed before.";

        } 

        return $output;
    }    

    //constructs message mailer object for bulk sends and also single/test sends
    public static function setupMessageMailer($db,ContainerInterface $container,$message_id,&$subject,&$body,&$error) 
    {
        $error = '';
        $subject = '';
        $body = '';
        $error_tmp = '';

        $mailer = clone $container['mail'];

        $sql = 'SELECT M.message_id,M.template_id,M.subject,M.body_html, '.
                      'T.name as template,T.template_html '.
               'FROM con_message AS M '.
               'JOIN con_template AS T ON(M.template_id = T.template_id)'.
               'WHERE M.message_id = "'.$db->escapeSql($message_id).'" ';
        $message = $db->readSqlRecord($sql); 
        if($message == 0 ) {
            $error = 'Invaid message ID['.$message_id.']';
        } else {
            $subject = $message['subject'];
            $body = str_replace('{CONTENT}',$message['body_html'],$message['template_html']);
            
            //Template images
            $location_id = 'TMP'.$message['template_id'];
            $sql = 'SELECT file_id,file_name_orig,link_id FROM '.TABLE_PREFIX.'file '.
                   'WHERE location_id = "'.$location_id.'" ORDER BY file_id ';
            $template_files = $db->readSqlArray($sql);
            if($template_files != 0) {
                //get any embedded images for template wherever they might be
                $images = new Upload($db,$container,TABLE_PREFIX.'file');
                $images->setup(['location'=>'TMP','interface'=>'download']);

                foreach($template_files as $file_id => $file) {
                    $image_link = $file['link_id'];
                    //message templates format
                    $template_link = '{IMAGE:'.$image_link.'}';
                    //phpmailer expects following format
                    $mailer_link = '<img src="cid:'.$image_link.'">';
                    
                    $image_name = $file['file_name_orig'];
                    $image_path = $images->fileDownload($file_id,'FILE'); 
                    if(substr($image_path,0,5) !== 'Error' and file_exists($image_path)) {
                        $body = str_replace($template_link,$mailer_link,$body);
                        $mailer->AddEmbeddedImage($image_path,$image_link,$image_name);
                    } else {
                        $error .= 'Error fetching template image['.$image_name.'] for message!'; 
                    }   
                }   
            }

            //message attachments
            $location_id = 'MSG'.$message['message_id'];
            $sql = 'SELECT file_id,file_name_orig,file_size FROM '.TABLE_PREFIX.'file '.
                   'WHERE location_id = "'.$location_id.'" ORDER BY file_id ';
            $message_files = $db->readSqlArray($sql);
            if($message_files != 0) {
                $body .= '<br/>Please see attached documents('.count($message_files).').';

                //get any embedded images for template wherever they might be
                $files = new Upload($db,$container,TABLE_PREFIX.'file');
                $files->setup(['location'=>'MSG','interface'=>'download']);

                foreach($message_files as $file_id => $file) {
                    $file_name = $file['file_name_orig'];
                    $file_path = $files->fileDownload($file_id,'FILE'); 
                    if(substr($file_path,0,5) !== 'Error' and file_exists($file_path)) {
                        $mailer->addAttachment($file_path,$file_name);
                    } else {
                        $error .= 'Error fetching attachment['.$file_name.'] for message!'; 
                    }   
                } 
            }
        } 

        if($error === '') return $mailer; else return false; 
    }

    public static function setupBulkMessageMailer($db,ContainerInterface $container,$message_id,&$error) 
    {
        $error = '';
        $subject = '';
        $body = '';
        $error_tmp = '';

        $mailer = self::setupMessageMailer($db,$container,$message_id,$subject,$body,$error_tmp);
        if($error_tmp !== '') {
            $error .= 'Could not setup message: '.$error;
        } else {
            $from = ''; //will use default
            $param = [];
            $param['format'] = 'html';
            //NB: default is ALL but since we are using setupMessageMailer() to add attachements rather than via $param['attach'] we do not want to reset ALL
            $param['reset'] = 'TO'; 
            
            $mailer->setupBulkMail($from,$subject,$body,$param,$error_tmp);
            if($error_tmp != '') { 
                $error .= 'Error setting up queue mailer for message['. $message_id.']:'.$error_tmp; 
            }
                 
        } 

        if($error === '') return $mailer; else return false; 
    }

    public static function sendMessage($db,ContainerInterface $container,$message_id,$email_address,&$error) 
    {
        $error = '';
        $subject = '';
        $body = '';
        $error_tmp = '';

        $mailer = self::setupMessageMailer($db,$container,$message_id,$subject,$body,$error_tmp);
        if($error_tmp !== '') {
            $error .= 'Could not setup message: '.$error;
        } else {
            $mail_from = ''; //use default MAIL_FROM
            $mail_to = $email_address;
            $param = [];
            $param['format'] = 'html';
            //NB: default is ALL but since we are using setupMessageMailer() to add attachements rather than via $param['attach'] we do not want to reset ALL
            $param['reset'] = 'TO'; 
            $mailer->sendEmail($mail_from,$mail_to,$subject,$body,$error_tmp,$param);
            if($error_tmp != '') { 
                $error .= 'Error sending message to email['. $mail_to.']:'.$error_tmp; 
            } 
        }

        if($error === '') return true; else return false; 
    }    

    
    
    
}
<?php
namespace App\Risk;

use Seriti\Tools\SetupModuleData;
use Seriti\Tools\CURRENCY_ID;

use App\Risk\Helpers;

class SetupData extends SetupModuledata
{

    public function setupSql()
    {
        $this->tables = ['unit','objective','risk','control','indicator','indicator_result','control_link','owner','owner_link','action','action_result'];

        $this->addCreateSql('unit',
                            'CREATE TABLE `TABLE_NAME` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `id_parent` int(11) NOT NULL,
                              `title` varchar(255) NOT NULL,
                              `level` int(11) NOT NULL,
                              `lineage` varchar(255) NOT NULL,
                              `rank` int(11) NOT NULL,
                              `rank_end` int(11) NOT NULL,
                              PRIMARY KEY (`id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8'); 

        $this->addCreateSql('objective',
                            'CREATE TABLE `TABLE_NAME` (
                              `objective_id` int(11) NOT NULL AUTO_INCREMENT,
                              `unit_id` int(11) NOT NULL,
                              `name` varchar(255) NOT NULL,
                              `description` text NOT NULL,
                              `impact_id` int(11) NOT NULL,
                              `likely_id` int(11) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`objective_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('risk',
                            'CREATE TABLE `TABLE_NAME` (
                              `risk_id` int(11) NOT NULL AUTO_INCREMENT,
                              `objective_id` int(11) NOT NULL,
                              `name` varchar(255) NOT NULL,
                              `description` text NOT NULL,
                              `impact_id` int(11) NOT NULL,
                              `likely_id` int(11) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`risk_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('control',
                            'CREATE TABLE `TABLE_NAME` (
                              `control_id` int(11) NOT NULL AUTO_INCREMENT,
                              `name` varchar(255) NOT NULL,
                              `description` text NOT NULL,
                              `repeat_freq` varchar(16) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`control_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('control_link',
                            'CREATE TABLE `TABLE_NAME` (
                              `link_id` int(11) NOT NULL AUTO_INCREMENT,
                              `control_id` int(11) NOT NULL,
                              `link_to` varchar(64) NOT NULL,
                              `link_to_id` int(11) NOT NULL,
                              `mitigate_pct` int(11) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`link_id`),
                              UNIQUE KEY `idx_owner_link1` (`control_id`,`link_to`,`link_to_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('indicator',
                            'CREATE TABLE `TABLE_NAME` (
                              `indicator_id` int(11) NOT NULL AUTO_INCREMENT,
                              `link_to` varchar(64) NOT NULL,
                              `link_to_id` int(11) NOT NULL,
                              `name` varchar(255) NOT NULL,
                              `description` text NOT NULL,
                              `repeat_freq` varchar(16) NOT NULL,
                              `date_start` date NOT NULL,
                              `date_end` date NOT NULL,
                              `target_units` varchar(64) NOT NULL,
                              `target_value` decimal(12,2) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`indicator_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('indicator_result',
                            'CREATE TABLE `TABLE_NAME` (
                              `result_id` int(11) NOT NULL AUTO_INCREMENT,
                              `owner_id` int(11) NOT NULL,
                              `indicator_id` int(11) NOT NULL,
                              `date` datetime NOT NULL,
                              `value` decimal(12,2) NOT NULL,
                              `comment` text NOT NULL,
                              PRIMARY KEY (`result_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('owner',
                            'CREATE TABLE `TABLE_NAME` (
                              `owner_id` int(11) NOT NULL AUTO_INCREMENT,
                              `name` varchar(255) NOT NULL,
                              `email` varchar(255) NOT NULL,
                              `token` varchar(64) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`owner_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('owner_link',
                            'CREATE TABLE `TABLE_NAME` (
                              `link_id` int(11) NOT NULL AUTO_INCREMENT,
                              `owner_id` int(11) NOT NULL,
                              `link_to` varchar(64) NOT NULL,
                              `link_to_id` int(11) NOT NULL,
                              `link_type` varchar(64) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`link_id`),
                              UNIQUE KEY `idx_owner_link1` (`owner_id`,`link_to`,`link_to_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('action',
                            'CREATE TABLE `TABLE_NAME` (
                              `action_id` int(11) NOT NULL AUTO_INCREMENT,
                              `link_to` varchar(64) NOT NULL,
                              `link_to_id` int(11) NOT NULL,
                              `name` varchar(255) NOT NULL,
                              `description` text NOT NULL,
                              `repeat_freq` varchar(16) NOT NULL,
                              `date_start` date NOT NULL,
                              `date_end` date NOT NULL,
                              `progress_pct` int(11) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              PRIMARY KEY (`action_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

        $this->addCreateSql('action_result',
                            'CREATE TABLE `TABLE_NAME` (
                              `result_id` int(11) NOT NULL AUTO_INCREMENT,
                              `owner_id` int(11) NOT NULL,
                              `action_id` int(11) NOT NULL,
                              `date` datetime NOT NULL,
                              `progress_note` text NOT NULL,
                              `progress_pct` int(11) NOT NULL,
                              PRIMARY KEY (`result_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'); 

    }


    protected function afterProcess() {
      /*
        $message = Helpers::setupXXXX($this->db,'ALL'); 
        if($message !=='' ) {
            $this->process_count++;
            $this->addMessage($message);
        }    
      */

    }
    
}

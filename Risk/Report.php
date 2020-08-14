<?php
namespace App\Risk;

use Seriti\Tools\CURRENCY_ID;
use Seriti\Tools\Form;
use Seriti\Tools\Report AS ReportTool;

class Report extends ReportTool
{
     

    //configure
    public function setup() 
    {
        //$this->report_header = 'WTF';
        $param = [];
        $this->report_select_title = 'Select report';
        $this->always_list_reports = true;

        $param = ['input'=>['select_month_period','select_status','select_format']];
        $this->addReport('RISK','Risk summary',$param); 
        $this->addReport('RISK_CHART','Risk heatmap',$param);

        
        //$this->addInput('select_payoption','');
        $this->addInput('select_month_period',''); //Select report months
        $this->addInput('select_status',''); //Select report period
        $this->addInput('select_format',''); //Select Report format
    }

    protected function viewInput($id,$form = []) 
    {
        $html = '';
        
        if($id === 'select_pay_option') {
            $param = [];
            $param['class'] = 'form-control input-medium';
            $param['xtra'] = ['ALL'=>'All payment options'];
            $sql = 'SELECT option_id,name FROM '.TABLE_PREFIX.'option WHERE status = "OK" ORDER BY sort'; 
            if(isset($form['pay_option_id'])) $pay_option_id = $form['pay_option_id']; else $pay_option_id = 'ALL';
            $html .= Form::sqlList($sql,$this->db,'pay_option_id',$pay_option_id,$param);
        }

        if($id === 'select_month_period') {
            $past_years = 10;
            $future_years = 0;

            $param = [];
            $param['class'] = 'form-control input-small input-inline';
            
            $html .= 'From:';
            if(isset($form['from_month'])) $from_month = $form['from_month']; else $from_month = 1;
            if(isset($form['from_year'])) $from_year = $form['from_year']; else $from_year = date('Y');
            $html .= Form::monthsList($from_month,'from_month',$param);
            $html .= Form::yearsList($from_year,$past_years,$future_years,'from_year',$param);
            $html .= '&nbsp;&nbsp;To:';
            if(isset($form['to_month'])) $to_month = $form['to_month']; else $to_month = date('m');
            if(isset($form['to_year'])) $to_year = $form['to_year']; else $to_year = date('Y');
            $html .= Form::monthsList($to_month,'to_month',$param);
            $html .= Form::yearsList($to_year,$past_years,$future_years,'to_year',$param);
        }

        if($id === 'select_format') {
            if(isset($form['format'])) $format = $form['format']; else $format = 'HTML';
            $html.= Form::radiobutton('format','PDF',$format).'&nbsp;<img src="/images/pdf_icon.gif">&nbsp;PDF document<br/>';
            $html.= Form::radiobutton('format','CSV',$format).'&nbsp;<img src="/images/excel_icon.gif">&nbsp;CSV/Excel document<br/>';
            $html.= Form::radiobutton('format','HTML',$format).'&nbsp;Show on page<br/>';
        }

        return $html;       
    }

    protected function processReport($id,$form = []) 
    {
        $html = '';
        $error = '';
        $options = [];
        //$options['format'] = $form['format'];

        /*
        if($form['pay_option_id'] === 'ALL') {
            $html .= '<h2>(ALL payment options, values expressed in currency - '.CURRENCY_ID.')</h2>';
        } else {
            $pay_option = Helpers::getPayOption($this->db,TABLE_PREFIX,$form['pay_option_id']);
            $html .= '<h2>('.$pay_option['name'].', values expressed in currency - '.CURRENCY_ID.')</h2>';
        }    
        */
        
        if($id === 'RISK') {
            //$html .= Helpers::performanceReport($this->db,$form['portfolio_id'],$form['currency_id'],$form['from_month'],$form['from_year'],$form['to_month'],$form['to_year'],$options,$error);
             $error = 'Not coded yet';
            if($error !== '') $this->addError($error);
        }

        if($id === 'RISK_CHART') {
            //$html .= Helpers::getPortfolioChart($this->db,'performance',$form['portfolio_id'],$form['currency_id'],$form['from_month'],$form['from_year'],$form['to_month'],$form['to_year'],$options,$error);
            $error = 'Not coded yet';
            if($error !== '') $this->addError($error);
        }
                

        return $html;
    }

}
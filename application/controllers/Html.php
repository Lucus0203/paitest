<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Html extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model','company_model'));
		
		$this->_logininfo=$this->session->userdata('loginInfo');
        $roleInfo = $this->session->userdata('roleInfo');
        $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));

        $config['protocol']     = 'smtp';
        $config['smtp_host']    = '127.0.0.1';
        $config['smtp_user']    = 'mailservice';
        $config['smtp_pass']    = 'service';
        $config['smtp_port']    = '25';
        $config['charset']      = 'utf-8';
        $config['mailtype']     = 'html';
        $config['smtp_timeout'] = '5';
        $config['newline'] = "\r\n";
        $this->load->library ('email', $config);
		
	}
	

    public function about() {
        $this->load->view ( 'header' );
        $this->load->view ( 'html/about');
        $this->load->view ( 'footer' );

    }

    public function ability() {
        $this->load->view ( 'header' );
        $this->load->view ( 'html/ability');
        $this->load->view ( 'footer' );

    }

    public function abilityCustom(){
        $this->load->view ( 'header' );
        $this->load->view ( 'html/ability_custom');
        $this->load->view ( 'footer' );
    }

    public function price(){
        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            $this->session->set_userdata('action_uri', current_url());
            redirect('login', 'index');
        } else {
            $this->session->unset_userdata('action_uri');
            $this->load->view ( 'header' );
            $this->load->view ( 'html/price');
            $this->load->view ( 'footer' );
        }
    }

    public function downloadPrice(){
        $minbeds=$this->input->get('minbeds')*1;
        $minbeds01=$this->input->get('minbeds01')*1;
        $minbeds02=$this->input->get('minbeds02')*1;
        $requireSurvey=$this->input->get('requireSurvey')*1;
        $trainPlan=$this->input->get('trainPlan')*1;
        $wechat=$this->input->get('wechat')*1;
        $discount=$this->input->get('discount')*1;
        if($discount==1.8){
            $year=2;
            $free='9折';
        }elseif ($discount==2.55){
            $year=3;
            $free='8.5折';
        }else{
            $year=1;
            $free='原价';
        }
        $numval=$minbeds>1000?$minbeds*13:$minbeds*15;
        $amount=round((200*$minbeds01+2000*$minbeds02+$trainPlan*3000+$wechat*500)*$discount);
        $minbeds01show=($minbeds01>0)?'':'display:none;';
        $minbeds02show=($minbeds02>0)?'':'display:none;';
        $requireSurveyshow=($requireSurvey>0)?'':'display:none;';
        $trainPlanSurveyshow=($trainPlan>0)?'':'display:none;';
        $wechatshow=($wechat>0)?'':'display:none;';

        //sendmail
        $company = $this->company_model->get_row(array('code' => $this->_logininfo['company_code']));
        $message = "来自{$company['name']} ".$this->_logininfo['real_name']."的价格清单:
        <style>
        .red{color:#f00 !important;}
        .tableB {
            border-collapse: collapse;
            width: 100%;
            line-height: 1.6;
            font-size: 14px;
        }
        
        .tableB th {
            background-color: #f5f5f5;
            border: 1px solid #dbdbdb;
            border-left: none;
            border-right: none;
            color: #898989;
            text-align: center;
            padding: 10px 10px;
            font-weight: normal;
        }
        
        .tableB td {
            border: 1px solid #dbdbdb;
            border-left: none;
            border-right: none;
            text-align: left;
            padding: 10px 10px;
            color: #666666;
        }
        </style>
            <table class=\"tableB\">
                <col width=\"40%\" />
                <tr>
                    <td>功能</td>
                    <td>数量</td>
                    <td>价格</td>
                </tr>
                <tr>
                    <td>基础功能 </td>
                    <td class=\"minbedsnum\">".$minbeds."人 </td>
                    <td class=\"red minbedsAmount\" style=\"text-decoration:line-through;\">".$numval."</td>
                </tr>
                <tr class=\"minbeds01Tr\" style=\"".$minbeds01show."\">
                    <td>基础能力模型 </td>
                    <td class=\"minbeds01num\">".$minbeds01."个 </td>
                    <td class=\"red minbeds01Amount\">".($minbeds01*200)."</td>
                </tr>
                <tr class=\"minbeds02Tr\" style=\"".$minbeds02show."\">
                    <td>定制能力模型 </td>
                    <td>".$minbeds02."个 </td>
                    <td class=\"red minbeds02Amount\">".($minbeds02*2000)."</td>
                </tr>
                <tr class=\"surveyTr\" style=\"".$requireSurveyshow."\">
                    <td>年度需求调研</td>
                    <td>1年</td>
                    <td class=\"red\">免费</td>
                </tr>
                <tr class=\"trainPlanTr\" style=\"".$trainPlanSurveyshow."\">
                    <td>年度培训计划</td>
                    <td>1年</td>
                    <td class=\"red trainPlanAmount\">3000</td>
                </tr>
                <tr class=\"wechatTr\" style=\"".$wechatshow."\">
                    <td>个性化微信号</td>
                    <td>1个</td>
                    <td class=\"red wechatAmount\">500</td>
                </tr>
                <tr>
                    <td>时间</td>
                    <td>".$year."年</td>
                    <td class=\"red \">".$free."</td>
                </tr>
            </table>
            <p class=\"f16 aRight p15\">总价：<span class=\"red\" id=\"amount\">".$amount."元</span></p>";
        $message.='<p>发送时间'. date("Y年m月d日 H:i:s").'</p>';
        $this->email->clear();
        $tomail = 'rogerxin@live.cn';
        $this->email->from('service@trainingpie.com', $company['name']);
        $this->email->subject("{$company['name']} ".$this->_logininfo['real_name']."的价格清单");
        $this->email->to($tomail);//
        $this->email->message($message);
        $this->email->send();

        //echo '<script>window.close();</script>';

        //export
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel -> getDefaultStyle() -> getFont() -> setName("微软雅黑") -> setSize("14");//设置默认字体为微软雅黑，大小为14
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '功能')
            ->setCellValue('B1', '数量')
            ->setCellValue('C1', '价格')
            ->setCellValue('A2', '基础功能')
            ->setCellValue('B2', $minbeds.'人')
            ->setCellValue('C2', $numval)->getStyle('C2')->getFont()->setStrikethrough(true)->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
        $num=3;
        if($minbeds01>0){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, '基础能力模型')
                ->setCellValue('B'.$num, $minbeds01.'个')
                ->setCellValue('C'.$num, 200*$minbeds01)->getStyle('C'.$num)->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
            $num++;
        }
        if($minbeds02>0){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, '定制能力模型')
                ->setCellValue('B'.$num, $minbeds02.'个')
                ->setCellValue('C'.$num, 2000*$minbeds02)->getStyle('C'.$num)->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
            $num++;
        }
        if($requireSurvey>0){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, '年度需求调研')
                ->setCellValue('B'.$num, '1年')
                ->setCellValue('C'.$num, '免费')->getStyle()->getFont('C'.$num)->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
            $num++;
        }
        if($trainPlan>0){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, '年度培训计划')
                ->setCellValue('B'.$num, '1年')
                ->setCellValue('C'.$num, '3000')->getStyle()->getFont('C'.$num)->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
            $num++;
        }
        if($wechat>0){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, '个性化微信号')
                ->setCellValue('B'.$num, '1个')
                ->setCellValue('C'.$num, '500')->getStyle()->getFont('C'.$num)->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
            $num++;
        }
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$num, '时间')
            ->setCellValue('B'.$num, $year.'年')
            ->setCellValue('C'.$num, $free)->getStyle('C'.$num)->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.($num+1), '总计')
            ->setCellValue('C'.($num+1), $amount)->getStyle('C'.($num+1))->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );


        $objPHPExcel->getActiveSheet()->setTitle('价格清单');
        $objPHPExcel->setActiveSheetIndex(0);
        $name='价格清单';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }
	
}

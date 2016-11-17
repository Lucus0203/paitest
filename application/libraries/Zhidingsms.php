<?php
/**
 * Created by PhpStorm.
 * User: lucus
 * Date: 16/8/6
 * Time: 上午12:47
 */
class Zhidingsms
{
    var $_svr_url ='120.27.149.106:8030/service/httpService/httpInterface.do?';// 服务器接口路径
    var $_username = 'ZD30018';// 账号
    var $_password='4wxQJpbA';// 密码
    var $_veryCode='NGQRcBXQMCJW';// 通讯认证Key

    /**
     * 普通短信
     * @param $mobile
     * @param $msg
     */
    public function sendSMS($mobile, $msg){
        $post_data['username']=$this->_username;
        $post_data['password']=$this->_password;
        $post_data['veryCode']=$this->_veryCode;
        $post_data['method'] = 'sendMsg';
        $post_data['mobile'] = $mobile;
        $post_data['content']= $msg;
        $post_data['msgtype']= '1';       // 1-普通短信，2-模板短信
        $post_data['code']   = 'utf-8';   // utf-8,gbk
        $res = $this->request_post($this->_svr_url, $post_data);  // 如果账号开了免审，或者是做模板短信，将会按照规则正常发出，而不会进人工审核平台
        return $this->xml_to_array($res);
    }

    /**
     * 发送及时模板短信
     *
     * @param $mobile
     * @param $content
     * @param $tempid
     */
    public function sendTPSMS($mobile, $content,$tempid){
        $post_data['username']=$this->_username;
        $post_data['password']=$this->_password;
        $post_data['veryCode']=$this->_veryCode;
        $post_data['method'] = 'sendMsg';
        $post_data['mobile'] = $mobile;
        $post_data['content']= $content; //模板参数 @1@=包先生,@2@=
        $post_data['msgtype']= '2';             // 1-普通短信，2-模板短信
        $post_data['tempid'] = $tempid; // 模板编号'ZD10004-0000'
        $post_data['code']   = 'utf-8';         // utf-8,gbk
        $res = $this->request_post($this->_svr_url, $post_data);  // 如果账号开了免审，或者是做模板短信，将会按照规则正常发出，而不会进人工审核平台
        return $this->xml_to_array($res);
    }
    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    private function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }

        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }


    // XML格式转数组格式
    private function xml_to_array( $xml ) {
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)) {
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++) {
                $subxml= $matches[2][$i];
                $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )) {
                    $arr[$key] = $this->xml_to_array( $subxml );
                } else {
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }

    // 页面显示数组格式，用于调试
    private function echo_xmlarr($res) {
        $res = xml_to_array($res);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
    }
}
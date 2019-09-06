<?php

namespace Workerman\Basic;

use Exception;
use PDO;
use PDOException;
use DateTime;
use DateTimeZone;

class Utils {

  public static function UrlPath($tcp, $path) {
    // 成功解析调用完成 返回true, 否则返回false
    // restapi 专用处理 url路由格式 vmc/id , 其中 id为vmc dev_no
    $path = strtolower($path);
  //  self::_log($path);
    $a = explode("/", $path);

   $n = count($a);
    if ($n > 1) {
      if ($a[1] != "vmc" ) return false;
    }

    if($n == 3){
        return RestApi::call($tcp, $a[2]);
    }else if($n == 2){
        return RestApi::call($tcp,null);
    }
    return false;
  }
  
  public static function isChecker($name) {
    $rs = self::_query("select a.logname from zluser a where a.signer = '$name'");
    
    $i = count($rs);
    
    if ($i == 0) {
      return 0;
    }
    else {
      return 1;
    }
  }

  public static function _ts() {
    return date('YmdHis');
  }

  public static function _rnd($length = 8) {
    return rand(pow(10,($length-1)), pow(10,$length)-1);
  }

  public static function get_id() {
    return (self::_ts() . self::_rnd());
  }
  
  public static function updateTableField($t, $fid, $id, $f, $v, $name) {
    
    $flds = array("BATNO", "PNO", "CHK_LOG_1", "CHK_LOG_2", "CHK_LOG_3", "AMT");
    $ts   = array("ZYDJ", "ZYDM");
    if (in_array($f, $flds) && in_array($t, $ts)) {
      $rs = self::_query("exec updateTableField '$t', '$id', '$f', '$v', '$name'");
      return json_encode($rs);  
    }
    else {
      if (rtrim($v) == '') {
        $s = "update $t set $f = null where $fid = '$id'";
      }
      else {
        $s = "update $t set $f = '$v' where $fid = '$id'";
      }
      return self::_exec($s);
    }
  }

  public static function updatePicField($t, $fid, $id, $f, $v, $name) {

    $fn = self::base64_image_content($v, 'upload');
    
    if ($fn == false) {
      return "0";
    }
    else {
      $s = "update $t set $f = '$fn' where $fid = '$id'";
      return self::_exec($s);
    }
    
  }

  /**
   * [将Base64图片转换为本地图片并保存]
   * @param  [Base64] $base64_image_content [要保存的Base64]
   * @param  [目录] $path [要保存的路径]
   */
  public static function base64_image_content($base64_image_content, $path){
      //匹配出图片的格式
      if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
          $type = $result[2];
          $new_file = $path."/".date('Ymd',time())."/";
          if(!file_exists($new_file)){
              //检查是否有该文件夹，如果没有就创建，并给予最高权限
              mkdir($new_file, 0700);
          }
          $new_file = $new_file.time().".{$type}";
          if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
              return '/'.$new_file;
          }
          else{
              return false;
          }
      }
      else{
          return false;
      }
  }

  public static function djSummit($opr, $djid, $name) {
    $rs = self::_query("exec djSummit '$opr', '$djid', '$name'");
    return json_encode($rs);
  }

  public static function djQuery($ucid, $name, $sdt, $edt) {
    $rs = self::_query("exec djQuery '$ucid', '$name', '$sdt', '$edt'");
    return json_encode($rs);
  }

  public static function addDJByUser($sysid, $djcode, $name){
    $rs = self::_query("exec add_dj_by_name 'ZY', '$djcode', '$name'");
    return json_encode($rs);
  }

  public static function getJHMX($q, $name) {
    $rs = self::_query("select top 200 a.batno as id, '(' + a.batno + ')' + a.pno as no from rd_jhmxhelp a where a.batno >= '1758' and a.batno like '$q%' order by a.batno");
    return json_encode($rs);
  }

  public static function getZYType($djcode) {
    $rs = self::_query("select a.zy_no, a.zy_nm, a.zy_bz from zytype a where a.zy_no = '$djcode'");
    return $rs[0]["zy_bz"];
  }

  public static function getDJType($q, $name) {
    $q = trim($q);
    $rs = self::_query("select a.zy_no as id, '(' + a.zy_no + ')' + a.zy_nm as no from zytype a where a.zy_no like '$q%' order by zy_no");
    return json_encode($rs);
  }


  public static function getUnit2($q, $name) {
    $q = trim($q);
    $rs = self::_query("select a.uid as id, '(' + a.uno + ')' + a.uname as no from rd_unit2 a where (a.uno like '$q%') and activeflag = 'y' order by uno");
    return json_encode($rs);
  }

  public static function getPart($q, $name) {
    $rs = self::_query("select top 100 a.pno as id, '(' + a.pno + ')' + a.pname as no from rd_part_view a where a.pno like '$q%' or a.pname like '$q%' order by pno");
    return json_encode($rs);
  }

  public static function getPart2($q, $name) {
    $rs = self::_query("select top 100 a.pno as id, '(' + a.pno + ')' + a.pname as no from rd_part2 a where a.pno like '$q%' or a.pname like '$q%' order by pno");
    return json_encode($rs);
  }

  public static function getPart3($q, $name) {
    $rs = self::_query("select top 100 a.pno as id, '(' + a.pno + ')' + a.pname as no from rd_part3 a where a.pno like '$q%' or a.pname like '$q%' order by pno");
    return json_encode($rs);
  }

  public static function getPl1flag($q, $name) {
    $rs = self::_query("select top 100 a.pl1flag as id, '(' + a.pl1flag + ')' + a.pl1desc as no from pl1flag a where a.pl1flag like '$q%' or a.pl1desc like '$q%'");
    return json_encode($rs);
  }

  public static function getftype($q, $name) {
    $rs = self::_query("select top 100 a.ft_no as id, '(' + a.ft_no + ')' + a.ft_nm as no from ftype a where a.ft_no like '$q%' or a.ft_nm like '$q%'");
    return json_encode($rs);
  }

  public static function getDJByUser($sysid, $djcode, $djop, $name){
    $rs = self::_query("exec get_dj_by_name 'ZY', '$djcode', $djop, '$name'");
    return json_encode($rs);
  }

  public static function getDJByDJNO($sysid, $djcode, $djno, $name){
    $rs = self::_query("exec get_dj_by_djno 'ZY', '$djcode', '$djno', '$name'");
    return json_encode($rs);
  }

  public static function getDMByDJNO($sysid, $djcode, $djno, $name){
    $rs = self::_query("exec get_dm_by_djno 'ZY', '$djcode', '$djno', '$name'");
    return json_encode($rs);
  }

  public static function getZYDJTypeByName($name){
    $rs = self::_query("exec get_zldj_by_name 'ZY', '$name'");
    return json_encode($rs);

  }

  public static function getZYQyTypeByName($name){
    $rs = self::_query("exec get_zlqy_by_name 'ZY', '$name'");
    return json_encode($rs);
  }

  public static function checkZYDJTypeByName($name){
    $rs = self::_query("exec check_zldj_by_name 'ZY', '$name'");
    return json_encode($rs);
  }

  public static function login($name, $pwd){
    $rs = self::_query("select a.PWD from zluser a where a.logname = '$name'");
    $i = count($rs);
    
    if ($i == 0) {
      return 0;
    }
    else {
      if ($rs[0]["PWD"] == $pwd) {
        return 1;
      }
      else {
        return 2;
      }
    }
  }

  public static function setPwd($curuser, $curpwd, $newpwd) {
    $rs = self::_query("select a.PWD from zluser a where a.logname = '$curuser'");
    $i = count($rs);
    
    if ($i == 0) {
      return 0;
    }
    else {
      if ($rs[0]["PWD"] == $curpwd) {
        // 更新密码
        return self::_exec("update zluser set pwd = '$newpwd' where logname = '$curuser'");
      }
      else {
        return 2;
      }
    }
  }

  public static function getMonthStart() {
    return date("Y") . "-" . date("m") . "-" . "01" . " 00:00:00";
  }

  public static function getMonthEnd() {
    return date("Y") . "-" . date("m") . "-" . date("t") . " 23:59:59";
  }

  public static function getUserCond($qytype, $name) {
    $rs = self::_query("select a.* from USER_COND a where a.logname = '$name' and qy_type = '$qytype'");
    $i = count($rs);
    
    if ($i == 0) {
      $sdt = self::getMonthStart();
      $edt = self::getMonthEnd();
      // 增加一个该用户的查询条件
      $c = self::_exec("insert into USER_COND(logname, qy_type, sdt, edt) values ('$name', '$qytype', '$sdt', '$edt')");
      $rs = self::_query("select a.* from USER_COND a where a.logname = '$name' and qy_type = '$qytype'");
    }
    
    return json_encode($rs);
  }
  
  //去除引号
  public static function _esc($s) {
    $s = str_replace("'", "", $s);
    $s = str_replace('"', "", $s);
	return $s;
  }
  
  public static function _utf8(&$a) {
    foreach($a as &$row) {
      foreach($row as &$item)
      {
        if ($item == null) {
          $item = "";
        }
        
        if (is_string($item)) {
          $item = iconv("GB2312", "UTF-8", $item);
        }
      }
    }
  }

  public static function _gb2312($s){
    return iconv("UTF-8", "GB2312", $s);
  }

  public static function _query_mysql($sql, $urltype = 1, $showsql = 1){
      if ($showsql == 1) {
          self::_log($sql);
      }

      $conn = CFG::_conn_mysql($urltype);

      $rs = $conn->query($sql);

      $errcode = $conn->errorCode();
      $errinfo = $conn->errorInfo();

      if ($errcode == "00000") {
          $a = $rs->fetchAll(PDO::FETCH_ASSOC);
//      var_dump($a);
          //self::_utf8($a);
//      var_dump($a);
          return $a;
      }
      else {
          self::_log("pdo errcode: " . $errcode);
          self::_log(json_encode($errinfo));
          return array(0=>array("isok"=>0,"err"=>"执行更新发生意外错误!"));
      }
  }
  
  public static function _query($sql,$dbname = "rdscgl") {
    self::_log($sql);

    $conn = CFG::_conn($dbname);
    self::_sql_log($conn, $sql);
    $rs = $conn->query($sql);

    $errcode = $conn->errorCode();
    $errinfo = $conn->errorInfo();

    if ($errcode == "00000") {
      $a = $rs->fetchAll(PDO::FETCH_ASSOC);
      self::_utf8($a);
      return $a;
    }
    else {
      Utils::_log("pdo errcode: " . $errcode);
      Utils::_log(json_encode($errinfo));
      return array(0=>array("isok"=>0,"err"=>"执行更新发生意外错误!"));
    }
    return $a;
  }

    public static function _exec_mysql($sql, $urltype = 1){
        self::_log($sql);

        $conn = CFG::_conn_mysql($urltype);

        $c = $conn->exec($sql);

        $errcode = $conn->errorCode();
        $errinfo = $conn->errorInfo();

        if ($errcode == "00000") {
            return $c;
        }
        else {
            self::_log("pdo errcode: " . $errcode);
            self::_log(json_encode($errinfo));
            return json_encode(array(0=>array("isok"=>0,"err"=>"执行更新发生意外错误!")));
        }
    }
    public static function _exec($sql,$dbname = "rdscgl"){
    self::_log($sql);
    
    $conn = CFG::_conn($dbname);
    self::_sql_log($conn, $sql);
    $c = $conn->exec($sql);
    //_log($c);
    return $c;
  }            

  public static function _sql_log($conn, $s) {
    return;
    //$conn = CFG::_conn();
    $name = self::_name();
    $s = str_replace("'", "''", $s);
    $sql_log = "insert into zlsql_log(log_date, log_name, log_sql) values (getdate(), '$name', '$s')";
    $c = $conn->exec($sql_log);
    //self::_log($sql_log);
    //self::_log($c);
  }
  
  public static function _name() {
    if (isset($_SESSION["user_info"])) {
      $user = json_decode($_SESSION["user_info"], true);
      return $user["name"];
    }  
    else {
      return '';
    }
  }

  public static function check_user() {
    if (isset($_SESSION["user_info"])) {
      $user = json_decode($_SESSION["user_info"], true);
      if ($user["login"] == 1) {
        return true;
      }
      else {
        return false;
      }
    }  
    else {
      return false;
    }
  }

  public static function not_login() {
    return json_encode(array("login"=>0));
  }

  public static function console($s){
    $name = self::_name();
    $stdout = fopen('php://stdout', 'w');
    fwrite($stdout, "$name " . "[" . date("Y-m-d H:i:s") . "] " . $s . "\r\n"); 
    fclose($stdout);
  }

  public static function _log($s, $_con=1,$_convert = 0) {
    if ($_con == 1) {
      if($_convert == 1){
		  self::console(iconv('utf-8','gb2312',$s));
	  }else{
		  self::console($s);	  
	  }
    }
    else {
      // $f = __DIR__ . "\\log\\" . date("ymd") . ".log";
      // file_put_contents($f, date("Y-m-d H:i:s") . "\n", FILE_APPEND);
      // file_put_contents($f, $s . "\n", FILE_APPEND);
    }
  }

  //1、Unix时间戳转日期  
  public static function ut2dt($unixtime, $timezone = 'PRC') {  
      if ($unixtime == 0) {
        return '';
      } 
      else {
        $datetime = new DateTime("@$unixtime"); //DateTime类的bug，加入@可以将Unix时间戳作为参数传入  
        $datetime->setTimezone(new DateTimeZone($timezone));  
        return $datetime->format("Y-m-d H:i:s");  
      }
  }  
    
  //2、日期转Unix时间戳  
  public static function dt2ut($date, $timezone = 'PRC') {  
      $datetime= new DateTime($date, new DateTimeZone($timezone));  
      return $datetime->format('U');  
  }  


  /**
   * 模拟post进行url请求
   * @param string $url
   * @param array $post_data
   */
  public static function request_post($url = '', $post_data = array()) {
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
  /*
   *发送短信
   *
   */
 
  public static function remote_sms_send($phone,$val_num,$msg) {
//    $url = "http://123.206.110.239:8090/service/webservice.asmx/Login";

    $url = "http://123.206.110.239:8090/2052/handler/smssend.ashx";

    $post_data['session_id']    = 'E60C1F7F-F014-4CC1-8F2D-7C815D24999A';
    $post_data['mobile_phone']  = $phone;
    $post_data['val_num']       = $val_num;
    $post_data['temp_password'] =  $msg;

    $res = self::request_post_asmx($url, $post_data);
    
    return $res;
    //self::_log($res);
  }
      
  public static function request_post_asmx($url, $post_data) {
    
    // POST /service/webservice.asmx/Login HTTP/1.1
    // Host: localhost
    // Content-Type: application/x-www-form-urlencoded
    // Content-Length: length

    // session_id=string&mobile_phone=string&val_num=string&temp_password=string


    $ch = curl_init();
    $s  = http_build_query($post_data);
  //  _log($url);
    
    //self::_log($s);
        
    curl_setopt($ch, CURLOPT_URL, $url);
      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
          "Content-length: " . strlen($s)));
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $s);
     
    if( ! $data = curl_exec($ch)) {
      trigger_error(curl_error($ch));
    } 
    
    curl_close($ch);

    return $data;  
  }
  //去除空格
  public static function _removeblank($str){
		return preg_replace('# #','',$str);    
  }
  //判断是否为手机号
  public static function _IsMobile($mobile_phone){
		$pattern = "/^[1][358][0-9]{9}$/";
		return preg_match($pattern,$mobile_phone);
  } 
  
  public static function getArgs($key) {
    if (array_key_exists($key, $_GET)) {
      $value = self::_esc($_GET[$key]);
    }
    else {
      $value = "";
    }
    return $value;
  }

    public static function logWithLevel($msg,$level=1){
      if($level >= CFG::$_log_level){
          self::_log($msg);
      }
    }

}



?>
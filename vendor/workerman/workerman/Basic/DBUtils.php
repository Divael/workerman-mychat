<?php
namespace Workerman\Basic;
use Workerman\Basic\Utils;

//整理各个部分调用数据库的SQL语句
//--调用结构---
//APP<-->DBUtils<-->Utils<-->DataBase

class DBUtils{
    private static $_is_log_on = true;

    public static function SetLaneState($did,$dl_no,$state){
        $sql = "update DEV_LANE set STATE = '$state' where DID = '$did' and E_P_NO = '$dl_no'";
        Utils::_exec($sql);
    }

    /**
     * @return array 设备信息数组
     */
    public static function GetDevs(){
        $sno = CFG::$_vmc_sno;
        $eno = CFG::$_vmc_eno;
        $rs = Utils::_query("select * from DEV_AS  where DID>='$sno' and DID<='$eno'",CFG::$_dbname);
        //print_r($rs);
        return $rs;
    }

    /**
     * 设置所有设备关闭
     */
    public static function AllDevsOff(){
        $sno = CFG::$_vmc_sno;
        $eno = CFG::$_vmc_eno;
        Utils::_exec("update DEV_AS set state = 4 where state =1 and DNO>='$sno' and DNO<='$eno'",CFG::$_dbname);
    }
    /**
     * 设置设备状态
     * @param $imei 设备IMEI
     * @param $_is_power_on 设备状态 true 正常 false 离线
     */
    public static function SwitchDevOnOff($imei,$_is_power_on){
        if($_is_power_on){
            Utils::_exec("update DEV_AS set state = 1 where dev_sn='$imei'",CFG::$_dbname);
        }else{
            Utils::_exec("update DEV_AS set state = 4 where dev_sn='$imei'",CFG::$_dbname);
        }
    }

    /**
     * 进行出货命令时，写入CHANNEL前，插入DEV_LOG1
     * @param $orderno 订单号
     * @param $did  设备DID
     * @param $laneno   货道号
     */
    public static function InsertDEVLOG1($orderno,$did,$laneno){
        Utils::_exec("insert into DEV_LOG1(ORD_ID,DID,LANE_NO,OP_DATE) VALUES ('$orderno','$did','$laneno','".date('Y/m/d H:i:s',time())."')",CFG::$_dbname);
    }

    /**
     * 进行出货命令时，写入CHANNEL前，插入DEV_LOG1
     * @param $orderno 订单号
     * @param $did  设备DID
     * @param $laneno   货道号
     * @param $laneno   出货结果
     */
    public static function LaneOutLog3($orderno,$did,$laneno,$slotRes,$amount){
        Utils::_exec("insert into DEV_LOG3(ORD_ID,DID,LANE_NO,OP_DATE,ST) VALUES ('$orderno','$did','$laneno','".date('Y/m/d H:i:s',time())."','$slotRes')",CFG::$_dbname);

    }

    public  static function UpdateMTDJ($orderno,$did,$laneno,$slotRes,$amount){
        $sql ="update DEV_LANE set MI_AMT = MI_AMT - $amount where DID = '$did' and E_P_NO = $laneno" ;
        Utils::_exec($sql,CFG::$_dbname);
    }


    /**
     * 记录订单
     * @return array
     */
    public static function InsertMTDJ($order,$is_refund = false,$refund_no = null){
        $order_no = $order["order_no"];
        $odj_id = null;
        $amt = 1;
        if($is_refund){
            if($refund_no == null){
                $order_no = Utils::get_id();
            }else{
                $order_no = $refund_no;
            }
            $odj_id = $order["order_no"];
            $amt = -1;
        }

        $sql = "INSERT INTO MTDJ
           ([DJ_ID]
           ,[DJ_TYPE]
           ,[MI_BAR]
           ,[WID]
           ,[WNM]
           ,[DJ_DATE]
           ,[MI_ID]
           ,[MI_NO]
           ,[MI_NM]
           ,[MI_MS]
           ,[AMT]
           ,[UP]
           ,[DJ_NOTE]
           ,[DJ_UPDATED]
           ,[CNO]
           ,[DJ_BT]
           ,[DJ_ET]
           ,[DJ_PAYED]
           ,[DID]
           ,[DL_NO]
           ,[PAY_TYPE]
           ,[PAY_NO]
           ,[DJ_ZF]
           ,[OP_USER]
           ,[OP_DATE]
           ,[OP_YEAR]
           ,[OP_MNTH]
           ,[ZJE]
           ,[ODJ_ID])
     VALUES
           ('" . $order_no . "'
           ,'05'
           ,null
           ,null
           ,null
           ,'" . date('Y/m/d H:i:s', time()) . "'
           ,'" . $order["mi_id"] . "'
           ,'" . $order["mi_no"] . "'
           ,'" . Utils::_gb2312($order["mi_nm"]) . "'
           ,'" . Utils::_gb2312($order["mi_ms"]) . "'
           ,$amt
           ,'".$order["total_amount"]."'
           ,null
           ,1
           ,''
           ,'" . date('Y/m/d H:i:s', time()) . "'
           ,'" . date('Y/m/d H:i:s', time()) . "'
           ,0
           ,'" . sprintf("%08d", $order["mid"]) . "'
           ,'" . $order["dl_no"] . "'
           ,'" . $order['pay_type'] . "'
           ,''
           ,0
           ,null
           ,'" . date('Y/m/d H:i:s', time()) . "'
           ,'" . date('Y', time()) . "'
           ,'" . date('m', time()) . "'
           ,'". $amt*$order["total_amount"] ."'
           ,'". $odj_id ."'
           )";

        $rs = Utils::_exec($sql,CFG::$_dbname);
        return $rs;
    }


    //检查订单是否插入
    public static function CheckMTDJ($order_no){
        $sql = "select 1 from MTDJ where dj_id = '".$order_no."'";
        $rs = Utils::_query($sql,CFG::$_dbname);
        if(isset($rs[0]["isok"]) && $rs[0]["isok"] == 0){
            self::_log($rs[0]["err"]);
            return false;
        }else{
            return count($rs) == 1;
        }
    }

    //查询订单
    public static function FindMTDJ($order_no){
        $sql = "select * from MTDJ where dj_id = '".$order_no."'";
        $rs = Utils::_query($sql,CFG::$_dbname);
        if(isset($rs[0]["isok"]) && $rs[0]["isok"] == 0){
            self::_log($rs[0]["err"]);
            return false;
        }else{
            return $rs;
        }
    }

    public static function FailureLog($fail_info,$fail_description){
        $charset = mb_detect_encoding($fail_info,"auto");
        $fail_info = iconv($charset,"gb2312",$fail_info);
        $charset = mb_detect_encoding($fail_description,"auto");
        $fail_description = iconv($charset,"gb2312",$fail_description);

        $sql = "insert into FAIL_LOG(fail_info,fail_date,fail_description) VALUES ('".$fail_info."','".date('Y/m/d H:i:s', time())."','".$fail_description."')";
        Utils::_query($sql,CFG::$_dbname);
    }
    /**
     * 类日志输出
     * @param $msg 日志信息
     */
    public static function _log($msg){
        if(self::$_is_log_on){
            Utils::_log($msg);
        }
    }
}
?>
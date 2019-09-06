<?php

use Think\Exception;
use \Workerman\Basic\Medoo;
use \Workerman\Basic\Utils;
use \Workerman\Lib\Timer;

if (isset($_POST["submit"]) && $_POST["submit"] == '  确 定  ') {
    $user  = $_POST["username"];
    $pwd =$_POST["password"];
    var_dump("用户名{$user}  -   密码{$pwd}");
} else {
    echo "<script>alert('提交未成功！'); window.location.href = 'index.php'</script>";//history.go(-1);
}

$db = new Medoo([
    'database_type' => 'mssql',
    'database_name' => 'hnwb',
    'server' => 'app.jpkj.tech',
    'username' => 'hnwb',
    'password' => 'hnwb@123'
]);

$datas = $db->exec("select username,userpwd from test_user");

printf('数据库连接成功!'."\n");

$flag = false;

foreach ($datas as $data) {
    if ($user == $data['username'] && $pwd == $data['userpwd']) {
        try {
            Timer::del($GLOBALS['timer_id']);
        } catch (Exception $ex) {
            Utils::_log("Timer::del {$ex->getMessage()}");
        }
        print '登录成功!'."\n";
        echo "
        <script>
        alert('登录成功!');
        window.location.href='Main/main.php';
        </script>
        ";
        Utils::_log("登录成功");
        //Timer::delAll(); window.location.href='www.baidu.com';
        $flag  = true;
    }
}
if (!$flag) {
    echo "<script>alert('密码错误,请重新输入！'); history.go(-1);</script>";
}

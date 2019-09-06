<?php
$q = isset($_POST['q'])? $_POST['q'] : '';
if(($q)) {
    $sites = array(
        'RUNOOB' => '菜鸟教程: http://www.runoob.com',
        'GOOGLE' => 'Google 搜索: http://www.google.com',
        'TAOBAO' => '淘宝: http://www.taobao.com',
    );
    foreach($sites as $x=>$x_value){ //用$x=>$x_value表是单个键值对
        if($q == $x){
            echo $x_value.PHP_EOL;
        }
    }

} else {
?>
<form action="" method="POST"> 
    <select name="q">
    <option value="">选择一个站点:</option>
    <option value="RUNOOB">Runoob</option>
    <option value="GOOGLE">Google</option>
    <option value="TAOBAO">Taobao</option>
    </select>
    <input type="submit" value="提交">
    </form>
<?php
}
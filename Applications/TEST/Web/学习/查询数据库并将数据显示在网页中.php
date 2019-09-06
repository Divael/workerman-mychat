<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1 align = "center">数据库查询</h1>
<table width="70%" border="1" cellpadding="0" cellspacing="0" align="center">
<tr>
<td>编号</td>
<td>名称</td>
<td>图片</td>
<td>操作</td>
</tr>

<?php
$db = new Workerman\Basic\Medoo([
    'database_type' => 'mssql',
    'database_name' => 'hnwb',
    'server' => 'app.jpkj.tech',
    'username' => 'hnwb',
    'password' => 'hnwb@123',
]);
$result = $db ->query('select DM_NO ,DM_NM,DM_IMG from [dbo].[dev_menu] where DID = '."'".'D0000413'."'".' order by DM_NO');
$attr = $result;
foreach($attr as $v)
{ 
    Utils::_log("编号 {$v['DM_NO']} 名称 {$v['DM_NM']} 图片 {$v['DM_IMG']}");
    echo "<tr>
    <td>{$v['DM_NO']}</td>
    <td>{$v['DM_NM']}</td>
    <td>{$v['DM_IMG']}</td>
    <td>
       <a href='Delete.php?code={$v[0]}'>删除</a>
       <a href='Update.php?code={$v[0]}'>修改</a>
    </td>
    </tr>";
}
?>

</table>
<div><a href="Add.php">添加数据</a></div>
</body>
</html>

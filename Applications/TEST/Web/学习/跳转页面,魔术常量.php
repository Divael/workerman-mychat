<head> 
<script language="javascript">

function old_page() 
{ 
window.location = "../index.php" 
} 
function replace() 
{ 
window.location.replace("../index.php") 
} 
function new_page() 
{ 
window.open("../index.php") 
} 
</script> 
</head> 
<body> 
<input type="button" onclick="new_page()" value="在新窗口打开"/> 
<input type="button" onclick="old_page()" value="跳转后有后退功能"/> 
<input type="button" onclick="replace()" value="跳转后没有后退功能"/> 
</body>

<?php
echo "<h1>".'魔术常量'."</h1>";
echo '__FILE__该文件位于 " '  . __FILE__ . ' " ' . "<br>";
echo '__LINE__这是第 " '  . __LINE__ . ' " 行' . "<br>";
echo '__DIR__该文件目录位于 " '  . __DIR__ . ' " ' . "<br>";
echo '__CLASS__类名为：'  . __CLASS__ . "<br>";
echo '__FUNCTION__函数名为：' . __FUNCTION__  . "<br>";
echo '__METHOD__函数名为：' . __METHOD__ .'返回该方法被定义时的名字（区分大小写' . "<br>";
echo '__NAMESPACE__命名空间为："', __NAMESPACE__, '"' . "<br>"; // 输出 "MyProject"
?>
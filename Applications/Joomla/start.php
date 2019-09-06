<?php
/**
 *  php D:\Projects\aPHP\workerman-chat\Applications\Joomla\start.php start
 *
 *
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;
use \Workerman\Lib\Timer;
use \Workerman\Basic\Medoo;

require_once __DIR__ . '/../../vendor/autoload.php';

// WebServer
$web = new WebServer("http://0.0.0.0:8080");
// WebServer进程数量
$web->count = 2;
// 设置站点根目录
$web->addRoot('www.local.com', __DIR__.'/Web');

$web->name = 'TEST';

$web->onWorkerStart = function ($web) {
    /**
     * $web->id 进程id 从0- $web->count
     */
    if ($web->id == 0) {
        echo "Web服务1已启动！ {$web->id}\n";
    } else {
        echo "Web服务2已启动！{$web->id}\n";
    };
    //print 'Web服务已启动！';


    // 每2.5秒执行一次
    $time_interval = 2.5;
    //$GLOBALS['timer_id']
    $GLOBALS['timer_id'] = Timer::add($time_interval, function () {
        echo "task run\n";
    });


    
    // $database = new Medoo([
    //     'database_type' => 'mssql',
    //     'database_name' => 'hnwb',
    //     'server' => 'app.jpkj.tech',
    //     'username' => 'hnwb',
    //     'password' => 'hnwb@123'
    // ]);

    // $table = $database ->exec('select did,dnm from dev_as');

    // foreach($table as $data)
    // {
    //     echo "   did:" . $data["did"] . " - dnm:" . $data["dnm"] . "\n";
    // }
};

$web->onMessage = function ($connection, $msg) {
    print "onMessage! {$connection} + {$msg}\n";
};

$web->onConnect = function ($connection) {
    echo "new connection from ip " . $connection->getRemoteIp() . "\n";
};

$web->onClose = function ($connection) {
    echo "connection closed\n";
};

$web-> onWorkerStop = function ($web) {
    echo "onWorkerStop stoped\n";
};

$web->onError = function ($connection, $code, $msg) {
    echo "error $code $msg\n";
};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

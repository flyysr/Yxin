<?php
/**
 * Author: fly
 * Date: 2018/10/16
 * Time: 13:41
 * email: 981883873@qq.com
 */
include "db.php";
include "config.php";


$requestUrl = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$controllerAndAction = explode('?', $requestUrl)[0];

/**
 * query Parameters
 */
$queryParams = new stdClass();
$queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
if(strpos($queryString, '=')){
    if(strpos($queryString, '&')){
        $queryString = explode('&', $queryString);
        foreach ($queryString as $q){
            if(strpos($q, '=')){
                $kv = explode('=', $q);
                $queryParams->$kv[0] = $kv[1];
            }
        }
    }else{
        $kv = explode('=', $queryString);
        $queryParams->$kv[0] = $kv[1];
    }
}


/**
 * @path        /login
 * @method      POST
 */
function login($dbConfig, $queryParams)
{
    $db = new \Yxin\DB($dbConfig['host'], $dbConfig['port'], $dbConfig['db'], $dbConfig['username'], $dbConfig['password']);
    $user = $db->query("select a.passwd, u.name from dbusers u LEFT JOIN accounts a on u.name=a.name   where u.name ='dnxdev|".$_POST['username']."';");
    if(sha1($_POST['password']) == $user[0]['passwd']){
        return "login success";
    }
    var_dump($user);

    return 'login executed!';
}

/**
 * @path /dbusers/projectList
 */
function projectList($dbConfig)
{
    $db = new \Yxin\DB($dbConfig['host'], $dbConfig['port'], $dbConfig['db'], $dbConfig['username'], $dbConfig['password']);
    $projects = $db->query('select * from scada_projects limit 10;');
    var_dump($projects);
    return "projectList executed!";
}

/**
 * @path /test
 * @method GET
 */
function test(){
    echo 'test';
}

/**
 *  @path   /alarms
 */
function alarms(){
    echo "alarms";
}
/**
 * @path  /message
 * @method POST/GET
 */
function message(){
    echo "message";
}

/**
 * route map array
 *
 */
$routeMap = [];

$internalFuns = get_defined_functions()['user'];
foreach ($internalFuns as $k => $func) {
    $f = new ReflectionFunction($func);
    $docComment = $f->getDocComment();
    $lines = explode(PHP_EOL, $docComment);
    $funConfig = [];
    $funConfig['fun'] = $f;
    foreach ($lines as $num => $line) {
        $comment = explode('@', $line);
        if (!empty(@$comment[1])) {
            $comment = $comment[1];
            $params = preg_split('/\s+/', $comment);

            if($params[0] == 'path'){
                $funConfig['path'] = $params[1];
            }
            if($params[0] == 'method'){
                $funConfig['method'] = explode('/', preg_replace('/\s+/', '', $params[1]));
            }
        }

    }
    if(!empty($funConfig) && !empty($funConfig['path'])){
        if(!isset($funConfig['method'])){
            $funConfig['method'] = ['GET'];
        }
        $routeMap[$funConfig['path']] = $funConfig;
    }
}
//var_dump($routeMap);exit;

function logAccess($status = 200) {
    file_put_contents("php://stdout", sprintf("[%s] %s:%s [%s]: %s\r\n",
        date("D M j H:i:s Y"), $_SERVER["REMOTE_ADDR"],
        $_SERVER["REMOTE_PORT"], $status, $_SERVER["REQUEST_URI"]));
}
/**
 * execute the page function
 */
if(array_key_exists($controllerAndAction, $routeMap) && in_array($requestMethod, $routeMap[$controllerAndAction]['method'])) {

    $response = $routeMap[$controllerAndAction]['fun']->invoke($dbConfig, $queryParams);
    var_dump($response);

    logAccess();
}else{

    header("Http/1.1 404 NOT_FOUND_PAGE");
    logAccess(404);
}

exit;






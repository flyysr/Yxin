<?php
/**
 * Author: fly
 * Date: 2018/10/16
 * Time: 13:41
 * email: 981883873@qq.com
 */

$requestUrl = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$controllerAndAction = explode('?', $requestUrl)[0];

/**
 * query Parameters
 */
$queryParams = new stdClass();
$queryString = $_SERVER['QUERY_STRING'];
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
 * @path        /dbusers/login
 * @method      POST
 */
function login($queryParams)
{
    echo('login');
    echo $queryParams->name;
    return 'login executed!';
}

/**
 * @path /dbusers/projectList
 */
function projectList()
{
    echo('project list');
    return "projectList executed!";
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
//                $routeMap[$params[1]] = [
//                    'f' => $f,
//                    'method' =>
//                ];
            }
            if($params[0] == 'method'){
                $funConfig['method'] = $params[1];
            }
        }

    }
    if(!empty($funConfig)){
        if(!isset($funConfig['method'])){
            $funConfig['method'] = 'GET';
        }
        $routeMap[$funConfig['path']] = $funConfig;
    }
}

/**
 * execute the page function
 */
if(array_key_exists($controllerAndAction, $routeMap) && $requestMethod == $routeMap[$controllerAndAction]['method'] ){
    $routeMap[$controllerAndAction]['fun']->invoke($queryParams);
}else{
    header("Http/1.1 404 NOT_FOUND_PAGE");
}

exit;






<?php

/*site define*/
define('HOST_NAME',env('ENV_HOST_NAME'));
define('CACHE_TIME', 1);
define('SHORT_CACHE_TIME', 10);

/* base path */
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

/* resources */
define('RESOURCES_PATH', ROOT_PATH .'/resources');
define('VIEW_PATH', RESOURCES_PATH . '/' . HOST_NAME . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('BUILD_PATH', HOST_NAME.'/build');
define('STATIC_URI_PREFIX', '/'.HOST_NAME.'/build');

/* ajax http code */
define('AJAX_SUCCESS',200);
define('AJAX_INFO',201);
define('AJAX_DATA_END',210);
define('AJAX_ARGUMENT_ERROR',400);
define('AJAX_SIGNATURE_ERROR',401);
define('AJAX_FORBIDDEN',403);
define('AJAX_DATA_EMPTY',404);
define('AJAX_METHOD_NOT_ALLOW',405);
define('AJAX_UN_PROCESSABLE_ENTITY',422);
define('AJAX_TOO_MANY_ATTEMPTS',428);
define('AJAX_TOO_MANY_REQUEST',429);
define('AJAX_SERVER_ERROR',500);
define('HISTORY_NUM', 12);
define('HISTORY_CACHE_TIME', 43200);
define('RECOMMEND_NUM', 12);

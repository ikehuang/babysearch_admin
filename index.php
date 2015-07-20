<?php
//$mode = 'staging';
$mode = 'development';
//$mode = 'staging';

date_default_timezone_set('Asia/Taipei');

if ($mode !== 'production') {
	ini_set('display_errors', 'on');
} else {
	ini_set('display_errors', 'off');
}

ini_set('memory_limit', '100024M');

require 'vendor/autoload.php';
require 'config.php';

!SessionNative::started() && SessionNative::start();

$app = new \Slim\Slim(array(
	'mode' => $mode,
	'view' => new \Slim\Views\Twig()
));
$app->add(new \Session());
//$app->add(new \CsrfGuard('csrf_key'));
$app->setName('TRACKER_iPET');
$app->configureMode( $mode, function () use ( $app, $mode, $APP_SETTINGS, $APP_CONFIG ) {

	$config = array_merge( $APP_SETTINGS[$mode], 
		$APP_CONFIG, 
		array(
			'mode'	=> $mode
		)
	);

	$app->config( $config );
	$view = $app->view();

	$view->parserExtensions = array(
		//'Twig_Extensions_Extension_I18n',
        'Twig_Extensions_Extension_Text'
    );

	$env = $app->environment();
    /**
     * Replace the QUERY_STRING with Nginx vhost.
     */
    if (preg_match('#(?P<request_uri>[\w/.\-_]+\?)#', $env['QUERY_STRING'], $m)) {
        $query_string = str_replace($m['request_uri'], '', $env['QUERY_STRING']);
        $env->offsetSet('QUERY_STRING', $query_string);
        $path_info = str_replace('?'.$query_string, '', $env['PATH_INFO']);
        $env->offsetSet('PATH_INFO', $path_info);

        $_rewrite_params = explode('&', $query_string);
        foreach($_rewrite_params as $param) {
            $_param = explode('=', $param);
            if (!isset($_GET[$param[0]])) {
                $_GET[(string) $_param[0]] = isset($_param[1]) ? (string) $_param[1] : '';
            }
        }
    }

    $resourceUri = $app->request()->getResourceUri();

	$view->appendData(array(
		'env' => array(
			'mode'	=> $mode,
			'app_name' => $app->getName(),
			'debug' => $APP_SETTINGS[$mode]['debug'],
			'url_scheme' => $env['slim.url_scheme'],
			'resourceUri' => $resourceUri,
			'rootUri' => $app->request()->getRootUri(),
			'isIE' => (boolean) preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])
		)
	));
	
	$app->view($view);
});

require 'hook.php';
require 'apps.php';
require 'models.php';
require 'routes.php';
require 'i18n.php';

/* Set default locale language */
$locale = $app->getCookie('locale');
if( empty($locale) ) {
	preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
	$locale = ($lang_parse[1][0] === 'zh-tw' or $lang_parse[1][0] === 'zh') ? 'zh_tw' : 'en';
	$app->setCookie('locale', $locale);
}
$app->view()->appendData(array('locale'	=> $locale, 'locale_text' => $I18N_DOCUMENT[$locale]));

$app->error(function (\Exception $e) use ($app) {
	echo $e->getMessage();
});

$app->run();

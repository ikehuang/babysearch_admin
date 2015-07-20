<?php
/*
* Dashboard Page Routing
* @Object dashboardApp
*
**/

$app->group('()', function( $index = null ) use ( $app, $authenticate ) {
    $dashdApp = new dashboardApp();
    /*
    * Render Pages
    **/
    $app->get('/login', function() use ( $app, $dashdApp ) { $dashdApp->render( 'login.phtml', '登入' ); });
    $app->get('/logout', function() use ( $app, $dashdApp ) { $dashdApp->logout(); });
    $app->get('/register', function() use ( $app, $dashdApp ) { $dashdApp->register_page(); });
    $app->get('(/)', $authenticate, function() use ( $app, $dashdApp ) { $dashdApp->user_info(); });
    $app->get('/user_info_edit', $authenticate, function() use ( $app, $dashdApp ) { $dashdApp->user_info_edit(); });
    $app->get('/active_product', $authenticate, function() use ( $app, $dashdApp ) { $dashdApp->active_product_page(); });
    $app->get('/pet_info_edit/:imei_code', $authenticate, function( $imei_code ) use ( $app, $dashdApp ) { $dashdApp->pet_info( $imei_code ); });

    $app->get('/i18n/:locale', function( $locale ) use ( $app, $dashdApp ) { $dashdApp->i18n( $locale ); });

    /*
    * Post Process
    **/
    $app->post('/login', function() use ( $app, $dashdApp ) { $dashdApp->login(); });
    $app->post('/register', function() use ( $app, $dashdApp ) { $dashdApp->register(); });
    $app->post('/user_info_edit', $authenticate, function() use ( $app, $dashdApp ) { $dashdApp->user_info_update(); });
    $app->post('/active_product', $authenticate, function() use ( $app, $dashdApp ) { $dashdApp->active_product(); });
    $app->post('/petinfo_update', $authenticate, function() use ( $app, $dashdApp ) { $dashdApp->pet_info_update(); }); 
});
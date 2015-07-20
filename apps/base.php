<?php
abstract class appBase {
	/**
    * Slim Object
    */
	protected $app;
    /**
    * Application Mode
    */
    protected $mode;

    public function __construct() {
		$this->app = \Slim\Slim::getInstance();
        $this->mode = $this->app->config('mode');
    }
    public function render( $page, $page_name = '', $info_data = array() ) {
        $default = array( 'page_name' => $page_name );
        $info = array_merge( $default, $info_data );
        $this->app->render('dashboard/'.$page, $info);
    }
    protected function render_json( array $result, $status = 200 ) {
        $this->app->applyHook('json.dispatch');
        $this->app->render('json.phtml', array(
            'result' => $result
        ), $status);
        $this->app->stop();
    }
    protected function error( $err_message = null, $redirect = '' ) {
        $this->app->flash('error', $err_message);
        $this->app->redirect( $redirect );
        $this->app->stop();
    }
}
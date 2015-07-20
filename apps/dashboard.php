<?php
final class dashboardApp extends appBase {

	public function logout() {
		SessionNative::delete('USER');
		$this->app->redirect('/login');
	}
	public function login() {
		$req = $this->app->request->params();
		$user = User::get_login($req['email'], $req['phone']);
		if( $user !== false ) {
			SessionNative::write('USER', $user);
			$this->app->redirect('/');
		} else {
			$this->error('登入資訊有誤，請重新輸入。', '/login');
		}
	}
	public function register_page() {
		$pet_type = PetType::get_all();
		$this->render( 'register.phtml', '建立新帳號', array( 'pet_type' => $pet_type ));
	}
	public function active_product_page() {
		$pet_type = PetType::get_all();
		$this->render( 'active_product.phtml', '啟用產品', array( 'pet_type' => $pet_type ));
	}

	public function i18n( $locale ) {
		$this->app->setCookie('locale', $locale );
		$this->app->redirect('/');
	}
	
	public function user_info() {
		$user = SessionNative::read('USER');
		$product_list = Product::get_by_user( $user['id'] );

		$this->render( 'user_info.phtml', '用戶資訊', array(
			'user'	=> $user,
			'list'	=> $product_list
		));
	}
	public function user_info_edit() {
		$user = SessionNative::read('USER');

		$this->render( 'user_info_edit.phtml', '用戶資訊編輯', array(
			'user'	=> $user
		));
	}
	public function user_info_update() {
		$req = $this->app->request->params();
		$result = User::update($req);
		if( $result === true ) {
			$user = User::get_by_email($req['email']);
			SessionNative::write('USER', $user);
			$this->app->redirect('/');
		} else {
			$this->error('資料更新失敗', '/user_info_edit');
		}
	}
	public function active_product() {
		$req = $this->app->request->params();
		$user = SessionNative::read('USER');
		$product = Product::get_by_imei_code( $req["imei_code"] );
		( $product === false ) and $this->error('IMEI碼輸入錯誤', '/active_product');
		$result = Product::update_register( $user['id'], $req["imei_code"], $req["pet_type"], $req["pet_name"] );
		if( $result === true ) {
			$this->app->redirect('/');
		} else {
			$this->error('資料更新失敗', '/active_product');
		}
	}
	public function pet_info( $imei_code ) {
		$pet_type = PetType::get_all();
		$product = Product::get_by_imei_code( $imei_code );

		$this->render( 'pet_info_edit.phtml', '寵物資料編輯', array( 'product' => $product, 'pet_type' => $pet_type ) );
	}
	public function pet_info_update() {
		$req = $this->app->request->params();
		$user = SessionNative::read('USER');
		$result = Product::update_pet( $user['id'], $req["imei_code"], $req["pet_type"], $req["pet_name"] );
		if( $result === true ) {
			$this->app->redirect('/');
		} else {
			$this->error('資料更新失敗', '/active_product');
		}
	}
	public function register() {
		$req = $this->app->request->params();
		if( $req['email'] === $req['email_repeat'] ) {
			$result = User::get_by_email( $req['email'] );
			if( $result === false ) {
				$product = Product::get_by_imei_code( $req["imei_code"] );
				( $product === false ) and $this->error('IMEI碼輸入錯誤', '/register');
				if( is_null($product['user_id']) and is_null($product['user_email']) ) {
					$user_id = User::add( $req );
					if( $user_id !== false ){
						$result = Product::update_register( $user_id, $req["imei_code"], $req["pet_type"], $req["pet_name"] );
						$user = User::get( $user_id );
						SessionNative::write('USER', $user);
						$this->app->redirect('/');
					} else {
						$this->app->flash('error', '資料寫入錯誤');
						$this->app->redirect('/register');
					}
				} else {
					$this->app->flash('error', '此產品已註冊');
					$this->app->redirect('/register');	
				}
			} else {
				$this->app->flash('error', '使用者已存在');
				$this->app->redirect('/register');
			}
		} else {
			$this->app->flash('error', '輸入email帳號有誤');
			$this->app->redirect('/register');
		}
	}
}
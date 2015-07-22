<?php

final class Product extends DBModule {
    public static function check_pending( $imei_code ) {
        $query = "SELECT *
                FROM `devices`
                WHERE `imei_code` = :imei_code AND `active_status` = 'pending'";

        $params = array( 'imei_code' => $imei_code );

        return self::_query( $query, $params );
    }
    public static function get_by_imei_code( $imei_code ) {
        $query = "select devices.*,users.nickname from devices left outer join users on users.email = devices.email where serial_number = :imei_code";

        $params = array( 'imei_code' => $imei_code );

        return self::_query( $query, $params );
    }
    public static function get_by_user( $user_id ) {
        $query = "SELECT *
                FROM `devices`";

        return self::_query_all( $query );
    }
    public static function get_list($page = 1) {
	$limit = 20;
  	$start = $page == 1 ? 0 : ($page-1)*$limit;
  	$start = $start <= 0 ? 0 : $start;

        $query = "SELECT did,serial_number,status,expiry_date,created,nickname 
        		FROM devices 
        		LEFT JOIN users 
        		ON devices.sso_id=users.sso_id
        		ORDER BY did limit {$start},{$limit}";
        return self::_query_all( $query );
    }
    public static function get_list_serial($page = 1) {
    	$limit = 20;
    	$start = $page == 1 ? 0 : ($page-1)*$limit;
    	$start = $start <= 0 ? 0 : $start;
    
    	$query = "SELECT did,serial_number,status,expiry_date,created,nickname
    	FROM devices
    	LEFT JOIN users
    	ON devices.sso_id=users.sso_id
    	ORDER BY devices.serial_number limit {$start},{$limit}";
    	return self::_query_all( $query );
    }
    public static function get_list_created($page = 1) {
    	$limit = 20;
    	$start = $page == 1 ? 0 : ($page-1)*$limit;
    	$start = $start <= 0 ? 0 : $start;
    
    	$query = "SELECT did,serial_number,status,expiry_date,created,nickname
    	FROM devices
    	LEFT JOIN users
    	ON devices.sso_id=users.sso_id
    	ORDER BY devices.created limit {$start},{$limit}";
    	return self::_query_all( $query );
    }
    public static function get_list_qtag($page = 1) {
	$limit = 20;
  	$start = $page == 1 ? 0 : ($page-1)*$limit;
  	$start = $start <= 0 ? 0 : $start;

        $query = "SELECT did,serial_number,status,expiry_date,created,nickname 
        		FROM devices 
        		LEFT JOIN users 
        		ON devices.sso_id=users.sso_id
			WHERE LOCATE('Q', devices.serial_number, 1)=3
        		ORDER BY did limit {$start},{$limit}";
        return self::_query_all( $query );
    }
    public static function get_list_ntag($page = 1) {
	$limit = 20;
  	$start = $page == 1 ? 0 : ($page-1)*$limit;
  	$start = $start <= 0 ? 0 : $start;

        $query = "SELECT did,serial_number,status,expiry_date,created,nickname 
        		FROM devices 
        		LEFT JOIN users 
        		ON devices.sso_id=users.sso_id
			WHERE LOCATE('N', devices.serial_number, 1)=3
        		ORDER BY did limit {$start},{$limit}";
        return self::_query_all( $query );
    }
    public static function get_list_btag($page = 1) {
	$limit = 20;
  	$start = $page == 1 ? 0 : ($page-1)*$limit;
  	$start = $start <= 0 ? 0 : $start;

        $query = "SELECT did,serial_number,status,expiry_date,created,nickname 
        		FROM devices 
        		LEFT JOIN users 
        		ON devices.sso_id=users.sso_id
			WHERE LOCATE('B', devices.serial_number, 1)=3
        		ORDER BY did limit {$start},{$limit}";
        return self::_query_all( $query );
    }	
    public static function get_list_by_pending() {
        $query = "SELECT `p`.`imei_code`, `p`.`dealer_name`, `p`.`dealer_shop`, `p`.`active_status`, `p`.`service_status`, `p`.`register_status`, `b`.`name`, `b`.`email`, `b`.`phone`, `b`.`gender`, `b`.`zipcode`, `b`.`address`
                FROM `devices` AS p
                INNER JOIN `buyers` AS b
                ON `b`.`id` = `p`.`buyer_id`
                AND `b`.`status` = 'active'
                WHERE `p`.`active_status` = 'pending'";

        return self::_query_all( $query );
    }
    
    public static function get_warranty_expiring() {
        $query = "SELECT *
                FROM `devices`
               ";

        return self::_query_all( $query );
    }
    public static function get_service_expiring() {
        $query = "SELECT *
                FROM `devices` AS p
                WHERE `p`.`service_status` = 'active' AND `p`.`service_expired` <= NOW()";

        return self::_query_all( $query );
    }
    public static function get_list_by_service_30days_expired() {
        $query = "SELECT `p`.*, `b`.`name`, `b`.`email`, `b`.`phone`, `b`.`gender`, `b`.`zipcode`, `b`.`address`
                FROM `devices` AS p
                INNER JOIN `buyers` AS b
                ON `b`.`id` = `p`.`buyer_id`
                AND `b`.`status` = 'active'
                WHERE `p`.`service_expired` >= NOW()
                AND `p`.`service_expired` <= :expired_30days";

        $today = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
        $expired_30days = date('Y-m-d H:i:s', strtotime($today . " + 30 day"));
        $params = array( 'expired_30days' => $expired_30days );
        return self::_query_all( $query, $params );
    }
    public static function get_service_expired() {
        $query = "SELECT `p`.*, `b`.`name`, `b`.`email`, `b`.`phone`, `b`.`gender`, `b`.`zipcode`, `b`.`address`
                FROM `devices` AS p
                INNER JOIN `buyers` AS b
                ON `b`.`id` = `p`.`buyer_id`
                AND `b`.`status` = 'active'
                WHERE `p`.`service_expired` <= NOW()";

        return self::_query_all( $query );
    }
    
	public static function add( $buyer_id = null, $data = array() ) {
		
		$serial_number	=& $data['serial_number'];
		$membership		=& $data['membership'];
	
		date_default_timezone_set("Asia/Taipei");
		$today = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
		$expired = date('Y-m-d H:i:s', strtotime($today . " + " . $membership . " year"));	
		
		if($expired == '1970-01-01 08:00:00')
			$expired = null;
		
		$status = 'new';
		
		$query = "INSERT INTO devices SET
				serial_number	=	:serial_number,
				status			=	:status,
				expiry_date		=	:expiry_date";
		
		$params = array(
				'serial_number'  => $serial_number,
				'status'         => $status,
				'expiry_date'    => $expired
		);
		
		$result = self::_insert( $query, $params );

		/*
        if( is_null($buyer_id) ) return false;
        //  $param =& param is equivalent to $param = isset($param)) ? $albert : NULL;
        $imei_code      =& $data['imei_code'];
        $dealer_num     =& $data['dealer_num'];
        $dealer_name    =& $data['dealer_name'];
        $dealer_shop    =& $data['dealer_shop'];

        $today = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
        $service_expired = date('Y-m-d H:i:s', strtotime($today . " + 366 day"));
        $warranty_expired = date('Y-m-d H:i:s', strtotime($today . " + 372 day"));

        $query = "INSERT INTO `devices` SET
                 `buyer_id`         = :buyer_id,
                 `imei_code`        = :imei_code,
                 `dealer_num`       = :dealer_num,
                 `dealer_name`      = :dealer_name,
                 `dealer_shop`      = :dealer_shop,
                 `actived_date`      = NOW(),
                 `service_expired`  = :service_expired,
                 `warranty_expired` = :warranty_expired,
                 `created`          = NOW(),
                 `modified`         = NOW()";

        $params = array(
            'buyer_id'          => $buyer_id,
            'imei_code'         => $imei_code,
            'dealer_num'        => $dealer_num,
            'dealer_name'       => $dealer_name,
            'dealer_shop'       => $dealer_shop,
            'service_expired'   => $service_expired,
            'warranty_expired'  => $warranty_expired
        );

        $result = self::_insert( $query, $params );
		*/
        return $result === true ? self::get_last_insert_id() : false;
	}
	public static function update_status( $list ) {
		
		$status = 'invalid';
	
		$array = (explode(",",$list));
		
		foreach($array as $serial_number) {
			
			$query = "UPDATE devices SET
					status			=	:status
					WHERE serial_number	=	:serial_number";
		
			$params = array(
					'serial_number'  => $serial_number,
					'status'         => $status
			);
		
			self::_update( $query, $params );
		}
	}
    public static function change_warranty_status( $imei_code = null, $status ) {
        if( is_null($imei_code) ) return false;
        $query = "UPDATE `devices` SET
                 `active_status` = :status,
                 `modified`      = NOW()
                 WHERE imei_code = :imei_code";

        $params = array(
            'imei_code'     => $imei_code,
            'status'        => $status
        );
    
        return self::_update( $query, $params );

    }
    public static function change_service_status( $imei_code = null, $status ) {
        if( is_null($imei_code) ) return false;
        $query = "UPDATE `devices` SET
                 `service_status` = :status,
                 `modified`      = NOW()
                 WHERE imei_code = :imei_code";

        $params = array(
            'imei_code'     => $imei_code,
            'status'        => $status
        );
    
        return self::_update( $query, $params );

    }
    public static function update( $buyer_id = null, $imei_code = null, $data ) {
    	
    	$membership         =& $data['membership'];
    	
    	//date_default_timezone_set("Asia/Taipei");
    	//$today = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
    	//$expired = date('Y-m-d', strtotime($today . " + " . $membership . " year"));
    	
    	//if($membership == 99)
    		//$expired = '9999-12-31';
    	
    	$expired = $membership;
    	
    	$query = "UPDATE devices SET
                 expiry_date  		= :expired,
                 WHERE serial_number = :imei_code";

    	$params = array(
    			'imei_code' => $imei_code,
    			'expired'   => $expired
    	);
    	
    	/*
    	if( is_null($buyer_id) || is_null($imei_code) ) return false;
        //  $param =& param is equivalent to $param = isset($param)) ? $albert : NULL;
        $dealer_num         =& $data['dealer_num'];
        $dealer_name        =& $data['dealer_name'];
        $dealer_shop        =& $data['dealer_shop'];
        $new_imei           =& $data['imei_num'];
        $service_expired    =& $data['service_expired'];
        $warranty_expired   =& $data['warranty_expired'];

        $query = "UPDATE `devices` SET
                 `dealer_num`       = :dealer_num,
                 `dealer_name`      = :dealer_name,
                 `dealer_shop`      = :dealer_shop,
                 `imei_code`        = :new_imei,
                 `service_expired`  = :service_expired,
                 `warranty_expired` = :warranty_expired,
                 `modified`     = NOW()
                 WHERE buyer_id = :buyer_id AND imei_code = :imei_code";

        $params = array(
            'buyer_id'          => $buyer_id,
            'imei_code'         => $imei_code,
            'dealer_num'        => $dealer_num,
            'dealer_name'       => $dealer_name,
            'dealer_shop'       => $dealer_shop,
            'new_imei'          => $new_imei,
            'service_expired'   => $service_expired,
            'warranty_expired'  => $warranty_expired
        );
        */
    
        return self::_update( $query, $params );

    }
    public static function update_register( $user_id = null, $imei_code = null, $pet_type, $pet_name ) {
        if( is_null($user_id) || is_null($imei_code) ) return false;

        $query = "UPDATE `devices` SET
                 `user_id`          = :user_id,
                 `pet_type`         = :pet_type,
                 `pet_name`         = :pet_name,
                 `active_status`    = 'pending',
                 `register_status`  = 'enable',
                 `modified`         = NOW()
                 WHERE imei_code    = :imei_code";

        $params = array(
            'user_id'   => $user_id,
            'imei_code' => $imei_code,
            'pet_type'  => $pet_type,
            'pet_name'  => $pet_name
        );
    
        return self::_update( $query, $params );

    }
    public static function update_pet( $user_id = null, $imei_code = null, $pet_type, $pet_name ) {
        if( is_null($user_id) || is_null($imei_code) ) return false;

        $query = "UPDATE `devices` SET
                 `pet_type`         = :pet_type,
                 `pet_name`         = :pet_name,
                 `modified`         = NOW()
                 WHERE imei_code    = :imei_code AND `user_id` = :user_id";

        $params = array(
            'user_id'   => $user_id,
            'imei_code' => $imei_code,
            'pet_type'  => $pet_type,
            'pet_name'  => $pet_name
        );
    
        return self::_update( $query, $params );

    }
    public static function delete($serial_number){
    	if(is_null($serial_number)) return false;
    	
    	$query = "DELETE FROM devices
    			WHERE serial_number = :serial_number";
    	
    	$params = array(
    			'serial_number' => $serial_number
    			);
    	/*
    	$query = "UPDATE devices SET
    			status = :status,
    			type = :type,
    			name = :name,
    			photo = :photo,
    			message = :message,
    			expiry_date = :expiry_date,
    			latitude = :latitude,
    			longitude = :longitude,
    			battery_status = :battery_status,
    			category = :category,
    			open = :open,
    			email = :email,
    			gid = :gid,
    			created = :created,
    			lost_message = :lost_message,
    			lost_date = :lost_date,
    			lost_time = :lost_time,
    			lost_location = :lost_location,
    			lost_spec = :lost_spec,
    			lost_contact_id = :lost_contact_id
    			WHERE serial_number = :serial_number";
    	
    	$params = array(
    			'status'   => 'new',
    			'type' => null,
    			'name'  => null,
    			'photo'  => null,
    			'message' => null,
    			'expiry_date' => null,
    			'latitude' => null,
    			'longitude' => null,
    			'battery_status' => null,
    			'category' => null,
    			'open' => null,
    			'email' => null,
    			'gid' => null,
    			'created' => null,
    			'lost_message' => null,
    			'lost_date' => null,
    			'lost_time' => null,
    			'lost_location' => null,
    			'lost_spec' => null,
    			'lost_contact_id' => null,
    			'serial_number' => $serial_number
    	);
    	*/
    	return self::_delete( $query, $params);
    }
}

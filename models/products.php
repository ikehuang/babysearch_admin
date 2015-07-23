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
	public static function reset_tag( $list ) {
	
		$reset = null;
		$status = 'new';
		
		$array = (explode(",",$list));
	
		foreach($array as $serial_number) {
				
			$query = "UPDATE devices SET
					status			=	:status,
					type			=	:type,
					name			=	:name,
					photo			=	:photo,
					message			=	:message,
					expiry_date			=	:expiry_date,
					latitude			=	:latitude,
					longitude			=	:longitude,
					battery_status			=	:battery_status,
					category			=	:category,
					open			=	:open,
					email			=	:email,
					gid			=	:gid,
					created			=	:created,
					lost_message			=	:lost_message,
					lost_date			=	:lost_date,
					lost_time			=	:lost_time,
					lost_location			=	:lost_location,
					lost_spec			=	:lost_spec,
					lost_contact_id			=	:lost_contact_id,
					sso_id			=	:sso_id,
					subcategory			=	:subcategory,
					lost_contact_id_2			=	:lost_contact_id_2,
					lost_contact_id_3			=	:lost_contact_id_3
					WHERE serial_number	=	:serial_number";
	
			$params = array(
					'serial_number'  => $serial_number,
					'status'         => $status,
					'type'         => $reset,
					'name'         => $reset,
					'photo'         => $reset,
					'message'         => $reset,
					'expiry_date'         => $reset,
					'latitude'         => $reset,
					'longitude'         => $reset,
					'battery_status'         => $reset,
					'category'         => $reset,
					'open'         => $reset,
					'email'         => $reset,
					'gid'         => $reset,
					'created'         => $reset,
					'lost_message'         => $reset,
					'lost_date'         => $reset,
					'lost_time'         => $reset,
					'lost_location'         => $reset,
					'lost_spec'         => $reset,
					'lost_contact_id'         => $reset,
					'sso_id'         => $reset,
					'subcategory'         => $reset,
					'lost_contact_id_2'         => $reset,
					'lost_contact_id_3'         => $reset
			);
var_dump("update device null");die();
			self::_update( $query, $params );
var_dump("update device null");	
			//also delete tag info
			$query = "select * from devices where serial_number = :serial_number";
			
			$params = array( 'serial_number' => $serial_number );
			
			$result = self::_query( $query, $params );
			
			if(!empty($result['type'])) {
			
				switch($result['type']) {
					
					case "Pets":
						$query = "UPDATE pet_info SET
						name			=	:name,
						sex			=	:sex,
						birthday			=	:birthday,
						height			=	:height,
						weight			=	:weight,
						temperament			=	:temperament,
						talents			=	:talents,
						description			=	:description,
						chip_number			=	:chip_number,
						desex			=	:desex,
						vaccine_type			=	:vaccine_type,
						bloodtype			=	:bloodtype,
						bloodbank			=	:bloodbank,
						disability			=	:disability,
						insurance			=	:insurance,
						hospital_name			=	:hospital_name,
						hospital_phone			=	:hospital_phone,
						hospital_address			=	:hospital_address,
						hospital_city			=	:hospital_city,
						hospital_district			=	:hospital_district,
						hospital_postal			=	:hospital_postal,
						hospital_country			=	:hospital_country
						WHERE did	=	:did";
						
						$params = array(
								'did'  				=> $result['did'],
								'name'         => $reset,
								'sex'         => $reset,
								'birthday'         => $reset,
								'height'         => $reset,
								'weight'         => $reset,
								'temperament'         => $reset,
								'talents'         => $reset,
								'description'         => $reset,
								'chip_number'         => $reset,
								'desex'         => $reset,
								'vaccine_type'         => $reset,
								'bloodtype'         => $reset,
								'bloodbank'         => $reset,
								'disability'         => $reset,
								'insurance'         => $reset,
								'hospital_name'         => $reset,
								'hospital_phone'         => $reset,
								'hospital_address'         => $reset,
								'hospital_city'         => $reset,
								'hospital_district'         => $reset,
								'hospital_postal'         => $reset,
								'hospital_country'         => $reset
						);
						break;
					case "Human":
						$query = "UPDATE human_info SET
						firstname			=	:firstname,
						lastname			=	:lastname,
						nickname			=	:nickname,
						sex				=	:sex,
						birthday			=	:birthday,
						height			=	:height,
						weight			=	:weight,
						bloodtype			=	:bloodtype,
						disease			=	:disease,
						disability			=	:disability,
						medications			=	:medications,
						hospital_name			=	:hospital_name,
						hospital_phone			=	:hospital_phone,
						hospital_address			=	:hospital_address,
						hospital_city			=	:hospital_city,
						hospital_district			=	:hospital_district,
						hospital_postal			=	:hospital_postal,
						hospital_country			=	:hospital_country
						WHERE did	=	:did";
							
						$params = array(
								'did'  				=> $result['did'],
								'firstname'         => $reset,
								'lastname'         => $reset,
								'nickname'         => $reset,
								'sex'         => $reset,
								'birthday'         => $reset,
								'height'         => $reset,
								'weight'         => $reset,
								'bloodtype'         => $reset,
								'disease'         => $reset,
								'disability'         => $reset,
								'medications'         => $reset,
								'hospital_name'         => $reset,
								'hospital_phone'         => $reset,
								'hospital_address'         => $reset,
								'hospital_city'         => $reset,
								'hospital_district'         => $reset,
								'hospital_postal'         => $reset,
								'hospital_country'         => $reset
						);
						break;
					case "Valuables":
						$query = "UPDATE valuable_info SET
						name			=	:name,
						description			=	:description,
						WHERE did	=	:did";
						
						$params = array(
								'did'  				=> $result['did'],
								'name'         => $reset,
								'description'         => $reset
						);
						break;
					default:
						break;
				}
				
				self::_update( $query, $params );
var_dump("update info null");
var_dump($query);			
				//delete photos
				$query = "DELETE FROM photos
    			WHERE did = :did";
				 
				$params = array(
						'did' => $result['did']
				);
			
				self::_delete( $query, $params);
var_dump("delete photo");			
				//delete guestbook
				$query = "DELETE FROM guestbook
    			WHERE did = :did";
					
				$params = array(
						'did' => $result['did']
				);
					
				self::_delete( $query, $params);
var_dump("delete guestbook");die();
			}
		}
	}
	public static function reopen_status( $list ) {
	
		$status = 'normal';
	
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
                 expiry_date  		= :expired
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
    public static function update_bulletin( $buyer_id = null, $id = null, $data ) {
    	 
    	$message         =& $data['message'];
    	 
    	//date_default_timezone_set("Asia/Taipei");
    	//$today = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
    	//$expired = date('Y-m-d', strtotime($today . " + " . $membership . " year"));
    	 
    	//if($membership == 99)
    	//$expired = '9999-12-31';
    	 
    	//$expired = $membership;
    	
    	$query = "UPDATE bulletin SET
                 message  		= :message
                 WHERE id = :id";
    
    	$params = array(
    			'id' => $id,
    			'message'   => $message
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
    public static function get_bulletin($page = 1) {
    	$limit = 20;
    	$start = $page == 1 ? 0 : ($page-1)*$limit;
    	$start = $start <= 0 ? 0 : $start;
    
    	$query = "SELECT *
    	FROM bulletin
    	LIMIT {$start},{$limit}";
    	return self::_query_all( $query );
    }
	public static function add_bulletin( $buyer_id = null, $data = array() ) {
		
		$message	=& $data['message'];
	
		date_default_timezone_set("Asia/Taipei");
		$created = date("Y-m-d", $_SERVER['REQUEST_TIME']);
		
		$status = 'normal';
		$display = 'y';
		
		$query = "INSERT INTO bulletin SET
				message			=	:message,
				created 		= 	:created,
				status			=	:status,
				display			=	:display";
		
		$params = array(
				'message'  		=> $message,
				'created'         => $created,
				'status'         => $status,
				'display'   	 => $display
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
	public static function get_bulletin_by_imei_code( $imei_code ) {
		$query = "select * from bulletin where id = :imei_code";
	
		$params = array( 'imei_code' => $imei_code );
	
		return self::_query( $query, $params );
	}
	public static function delete_bulletin($id){
		if(is_null($id)) return false;
		 
		$query = "DELETE FROM bulletin
    			WHERE id = :id";
		 
		$params = array(
				'id' => $id
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
	
	public static function toggle_bulletin( $id ) {
	
		$bulletin = self::get_bulletin_by_imei_code( $id );
		
		if($bulletin['display'] == 'y') {
			$bulletin['display'] = 'n';
			$bulletin['status'] = 'close';
		}
		else {
			$bulletin['display'] = 'y';
			$bulletin['status'] = 'normal';
		}
		
		$query = "UPDATE bulletin SET
		status  		= :status,
		display  		= :display
		WHERE id = :id";
	
		$params = array(
			'id' => $id,
			'status'   => $bulletin['status'],
			'display'   => $bulletin['display']
		);
	
		return self::_update( $query, $params );
	
		}
}

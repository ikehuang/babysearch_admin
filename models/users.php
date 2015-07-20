<?php

final class User extends DBModule {
	public static function get( $id ) {
		$query = "SELECT *
                FROM `users`
                WHERE `id` = :id";

        $params = array( 'id' => $id );

        return self::_query( $query, $params );
	}
	public static function get_by_email( $email ) {
		$query = "SELECT *
                FROM `users`
                WHERE `email` = :email";

        $params = array( 'email' => $email );

        return self::_query( $query, $params );
	}
	

	public static function get_login( $email, $password ) {	
		$status = 'enable';
		$query = "SELECT * 
			FROM `admins`
			WHERE `email` = :email AND `password` = :password AND `status` = :status LIMIT 1";
		$params = array(
			'email'  => $email,
			'password'  => md5($password),
			'status' => $status
		);
		
		return self::_query( $query, $params );
	}
	public static function add( $data ) {
        
        //  $param =& param is equivalent to $param = isset($param)) ? $albert : NULL;
		$name 		=& $data['name'];
		$email 		=& $data['email'];
		$phone 		=& $data['phone'];
		$gender 	=& $data['gender'];
		$zipcode 	=& $data['zipcode'];
		$address	=& $data['address'];

    	$query = "INSERT INTO `users` SET
    			 `name` 	= :name,
    			 `email` 	= :email,
    			 `phone` 	= :phone,
    			 `gender` 	= :gender,
    			 `zipcode` 	= :zipcode,
    			 `address` 	= :address,
    			 `created` 	= NOW(),
                 `modified` = NOW()";

    	$params = array(
    		'name'		=> $name,
			'email'		=> $email,
			'phone'		=> $phone,
			'gender'	=> $gender,
			'zipcode' 	=> $zipcode,
			'address'	=> $address
    	);
    	var_dump($params);

    	$result = self::_insert( $query, $params );

        return $result === true ? self::get_last_insert_id() : false;
	}
	public static function update( $data ) {
        //  $param =& param is equivalent to $param = isset($param)) ? $albert : NULL;
        $name       =& $data['name'];
        $email      =& $data['email'];
        $phone      =& $data['phone'];
        $gender     =& $data['gender'];
        $zipcode    =& $data['zipcode'];
        $address    =& $data['address'];

        $query = "UPDATE `users` SET
                 `name`     = :name,
                 `phone`    = :phone,
                 `gender`   = :gender,
                 `zipcode`  = :zipcode,
                 `address`  = :address,
                 `modified` = NOW()
                 WHERE `email` = :email";

        $params = array(
            'name'      => $name,
            'email'     => $email,
            'phone'     => $phone,
            'gender'    => $gender,
            'zipcode'   => $zipcode,
            'address'   => $address
        );
    
        return self::_update( $query, $params );

    }
}

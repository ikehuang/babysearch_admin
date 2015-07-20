<?php

final class Buyer extends DBModule {
    public static function get_by_email( $email ) {
        $query = "SELECT *
                FROM `buyers`
                WHERE `email` = :email";

        $params = array( 'email' => $email );

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

    	$query = "INSERT INTO `buyers` SET
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

    	$result = self::_insert( $query, $params );

        return $result === true ? self::get_last_insert_id() : false;
	}
    public static function update( $buyer_id = null, $data ) {
        if( is_null($buyer_id) ) return false;
        //  $param =& param is equivalent to $param = isset($param)) ? $albert : NULL;
        $name       =& $data['name'];
        $email      =& $data['email'];
        $phone      =& $data['phone'];
        $gender     =& $data['gender'];
        $zipcode    =& $data['zipcode'];
        $address    =& $data['address'];

        $query = "UPDATE `buyers` SET
                 `name`     = :name,
                 `email`    = :email,
                 `phone`    = :phone,
                 `gender`   = :gender,
                 `zipcode`  = :zipcode,
                 `address`  = :address,
                 `modified` = NOW()
                 WHERE `id` = :id";

        $params = array(
            'id'        => $buyer_id,
            'name'      => $name,
            'email'     => $email,
            'phone'     => $phone,
            'gender'    => $gender,
            'zipcode'   => $zipcode,
            'address'   => $address
        );
    
        return self::_update( $query, $params );

    }

    public static function delete( $id ) {
    
        $query = "DELETE FROM `buyers` WHERE `id` = '".$id."'";
        return self::_delete( $query );
    }
}
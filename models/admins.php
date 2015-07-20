<?php

final class Admin extends DBModule {

	public static function get( $email, $password ) {	
		
		$status = 'enable';
		$query = "SELECT * 
			FROM `admins`
			WHERE `email` = :email AND `password` = :password AND `status` = :status LIMIT 1";
		$params = array(
			'email'		=> $email,
			'password'	=> $password,
			'status'	=> $status
		);
		
		return self::_query( $query, $params );
	}
}

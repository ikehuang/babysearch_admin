<?php

final class PetType extends DBModule {
	public static function get_all() {
		$query = "SELECT *
                FROM `pet_types`";

        return self::_query_all( $query );
	}
}
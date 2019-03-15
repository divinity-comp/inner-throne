<?php

class EDD_Zapier_Customer extends EDD_Customer {

	public $first_name;
	public $last_name;

	public function get_first_name() {

		$names = explode( ' ', $this->name );
		return ! empty( $names[0] ) ? $names[0] : '';

	}

	public function get_last_name() {

		$names      = explode( ' ', $this->name );
		$last_name  = '';
		if( ! empty( $names[1] ) ) {
			unset( $names[0] );
			$last_name = implode( ' ', $names );
		}

		return $last_name;

	}

}
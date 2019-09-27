<?php

namespace Buma\Decorator;

use Buma\Decorator;

class DecoratorRooms extends Decorator {

	public function get_size() {
		return $this->_obj->meta['_room-size'][0] ?? __( 'ND', 'Buma' );
	}

	public function get_price( $key = 'medium' ) {
		return $this->_obj->meta[ sprintf( '_room-price-%s', $key ) ][0];
	}

	public function get_description(){
		return $this->_obj->meta['_room-description'][0];
	}

	public function get_guests(){
		return $this->_obj->meta['_room-guest'][0];
	}

	public function get_name(){
		return $this->_obj->meta['_room-name'][0];
	}

	public function get_image($size){
		return $this->_obj->images[$size];
	}
}
<?php

namespace Buma\Result\PostResult;

use Buma\FilterInput;
use Buma\Result\PostResult;
use Buma\Result\PostResultInterface;

class RoomSearchResult extends PostResult implements PostResultInterface {

	/**
	 * @return array
	 */
	public function get_args() {
		if ( empty( $this->_args ) ) {
			$metaArgs = $this->_options->white_list;
			$rows = ( new FilterInput( INPUT_GET, 'rows' ) )->get();
			$metas = ( new FilterInput( INPUT_GET, 'rows' ) )->get();
			foreach ($metas as $meta){

			}
			$args = [
				'rows' => $rows ?? $this->_options->results_per_page,
				'meta_query' => [
					[
						'key' => 'project_cat',
						'value' => 'my-value',
					]
				],
			];

			$this->set_args( $args );
		}

		return $this->_args;
	}

	/**
	 * Sets args for request
	 *
	 * @param array $args
	 *
	 * @return static
	 */
	public function set_args( array $args ) {
		$this->_args = $args;

		return $this;
	}

	/**
	 * Does the request
	 *
	 * @return PostResultInterface
	 */
	public function make_request(): PostResultInterface {
		$args = $this->get_args();

		$result = $this->get_result( $args );

		$this->set_rooms($result);

		return $this;
	}

	/**
	 * @param $args
	 *
	 * @return array
	 */
	protected function get_result( $args ): array {
		$rooms = [];
		$args  = array(
			'public'         => true,
			'post_type'      => 'rooms',
			'posts_per_page' => 10
		);

		$posts_results = get_posts( $args );

		if ( empty( $posts_results ) ) {
			return null;
		}

		foreach ( $posts_results as $post ) {
			$room          = new \stdClass();
			$postId        = $post->ID;
			$room->wp_post = $post;
			$room->title   = $post->post_title;
			$room->ID      = $postId;
			$room->url     = esc_url( get_permalink( $post ) );
			$room->meta    = get_post_meta( $postId );
			$room->images  = [
				'full'      => get_the_post_thumbnail_url( $postId, 'full' ),
				'medium'    => get_the_post_thumbnail_url( $postId, 'medium' ),
				'thumbnail' => get_the_post_thumbnail_url( $postId, 'thumbnail' )
			];

			$rooms[] = $room;
		}

		return $rooms;
	}

	/**
	 * Prepares the content object
	 *
	 * @return \stdClass
	 */
	public function build_content(): \stdClass {
		$content = new \stdClass();

		$content->items = $this->_arr['rooms'];

		return $content;
	}

}
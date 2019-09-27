<?php


namespace Buma\Cpt;


use Buma\MetaBox;

class Plates extends CustomPostType implements CptInterface, MetaBoxInterface {

	/**
	 * @var string
	 */
	protected $post_type = 'plates_page';

	/**
	 * @return CptInterface
	 */
	public function register_post_type(): CptInterface {
		$labels = [
			'name'               => _x( 'Dishes Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'singular_name'      => _x( 'Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'menu_name'          => _x( 'Dishes Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'name_admin_bar'     => _x( 'Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'parent_item_colon'  => _x( 'Parent Item:', 'Dish Restaurant CPT', 'Buma' ),
			'all_items'          => _x( 'All Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'add_new_item'       => _x( 'Add New Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'add_new'            => _x( 'Add New Dish', 'Dish Restaurant CPT', 'Buma' ),
			'new_item'           => _x( 'New Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'edit_item'          => _x( 'Edit Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'update_item'        => _x( 'Update Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'view_item'          => _x( 'View Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'search_items'       => _x( 'Search Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'not_found'          => _x( 'Not found', 'Dish Restaurant CPT', 'Buma' ),
			'not_found_in_trash' => _x( 'Not found in Trash', 'Dish Restaurant CPT', 'Buma' ),
		];
		$args   = [
			'label'               => _x( 'Dish Restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'description'         => _x( 'List menus of restaurant', 'Dish Restaurant CPT', 'Buma' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail', 'custom-fields', 'page-attributes', ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-carrot',
			'rewrite'             => [
				'slug' => 'menu'
			],
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		];
		register_post_type( 'plates_page', $args );

		$labels = array(
			'name'              => _x( 'Types', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Type', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Search Type', 'textdomain' ),
			'all_items'         => __( 'All Types', 'textdomain' ),
			'parent_item'       => __( 'Parent Type', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Type:', 'textdomain' ),
			'edit_item'         => __( 'Edit Type', 'textdomain' ),
			'update_item'       => __( 'Update Type', 'textdomain' ),
			'add_new_item'      => __( 'Add New Type', 'textdomain' ),
			'new_item_name'     => __( 'New Type Name', 'textdomain' ),
			'menu_name'         => __( 'Types', 'textdomain' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'types-dishes' ),
		);
		register_taxonomy( 'types-dishes', 'plates_page', $args );

		return $this;
	}

	/**
	 * @return MetaBoxInterface
	 */
	public function setup_metabox(): MetaBoxInterface {
		$args       = [
			'taxonomy'   => 'types-dishes',
			'hide_empty' => false,
			'orderby' => 'id'
		];
		$categories = get_terms( $args );
		$config = [
			$this->post_type => [
				'id'       => 'plates-meta-box',
				'title'    => 'Dish menu Options',
				'page'     => 'plates_page',
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => [
					[
						'name'  => __( 'Dish name', 'Buma' ),
						'id'    => '_plate-name',
						'type'  => 'text',
						'std'   => '',
						'title' => __( 'Dish name', 'Buma' ),
						'desc'  => __( 'Insert the name of dish', 'Buma' )
					],
					[
						'name'  => __( 'Dish name in english', 'Buma' ),
						'id'    => '_plate-eng-subtitle',
						'type'  => 'text',
						'std'   => '',
						'title' => __( 'Dish name in english', 'Buma' ),
						'desc'  => __( 'Insert the name of dish in english', 'Buma' )
					],
					[
						'name'        => __( 'Type of dish', 'Buma' ),
						'id'          => '_plate-type',
						'type'        => 'select',
						'placeholder' => 'Select type',
						'options'     => array_map( function ($category) {
							return $category->name;
						}, $categories),
						'std'         => '',
						'title'       => __( 'Type of dish', 'Buma' ),
						'desc'        => __( 'Select the type of dish', 'Buma' )
					],
					[
						'name'  => __( 'Dish price', 'Buma' ),
						'id'    => '_plate-price',
						'type'  => 'currency',
						'std'   => '',
						'title' => __( 'Dish price', 'Buma' ),
						'desc'  => __( 'Add price for dish', 'Buma' )
					],
					[
						'name'    => __( 'Note', 'Buma' ),
						'id'      => '_plate-note',
						'type'    => 'select',
						'options' => [
							'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
							'Pellentesque in tellus ornare, blandit nulla eget, convallis felis.',
							'Sed egestas consectetur rutrum, Nunc et lorem varius, aliquet lectus ut.'
						],
						'std'     => '',
						'title'   => __( 'Note', 'Buma' ),
						'desc'    => __( 'Select a note to add', 'Buma' )
					]
				]
			]
		];

		MetaBox::create()->add_config( $config );

		return $this;
	}
}
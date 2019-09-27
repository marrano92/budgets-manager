<?php

namespace Buma\Cpt;

use \Buma\MetaBox;

/**
 * Class Menus
 * @package Buma\Cpt
 */
class Menus extends CustomPostType implements CptInterface, MetaBoxInterface {

	/**
	 * @var string
	 */
	protected $post_type = 'menu_page';

	/**
	 * @return static
	 *
	 * @codeCoverageIgnore
	 */
	public function setup_metabox(): MetaBoxInterface {
		$args = [
			'post_type'        => 'plates_page',
			'post_status'      => 'publish'
		];
		$posts_array = get_posts( $args );
		$posts_titles = array_map( function ($post) {
			return ['value' => get_the_title($post) ];
		}, $posts_array);
		$config = [
			$this->post_type => [
				'id'       => 'menu-meta-box',
				'title'    => 'Menu Restaurant Options',
				'page'     => 'menu_page',
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => [
					[
						'name'  => __( 'Nome Menu', 'Buma' ),
						'id'    => '_menu-name',
						'type'  => 'text',
						'std'   => '',
						'title' => __( 'Nome Menu', 'Buma' ),
						'desc'  => __( 'Inserisci il nome del menu', 'Buma' )
					],
					[
						'name'  => __( 'Sottotitolo Menu', 'Buma' ),
						'id'    => '_menu-subtitle',
						'type'  => 'text',
						'std'   => '',
						'title' => __( 'Sottotitolo Menu', 'Buma' ),
						'desc'  => __( 'Inserisci il sottotitolo del menu', 'Buma' )
					],
					[
						'name'        => __( 'Tipologia Menu', 'Buma' ),
						'id'          => '_menu-type',
						'type'        => 'select',
						'placeholder' => 'Select an Item',
						'options'     => [
							'default' => 'default',
							'value1'  => 'value1'
						],
						'std'         => '',
						'title'       => __( 'Tipologia Menu', 'Buma' ),
						'desc'        => __( 'Seleziona la tipologia del menu', 'Buma' )
					],
					[
						'name'    => __( 'Piatti Menu', 'Buma' ),
						'id'      => '_menu-plate',
						'type'    => 'checkbox_list',
						'options' => $posts_titles,
						'std'     => '',
						'title'   => __( 'Piatti Menu', 'Buma' ),
						'desc'    => __( 'Aggiungi i piatti da inserire nel menu', 'Buma' )
					],
					[
						'name'  => __( 'Project ID', 'Buma' ),
						'id'    => '_menu-projectId',
						'type'  => 'text',
						'std'   => '',
						'title' => __( 'Project ID', 'Buma' ),
						'desc'  => __( 'Insert project ID', 'Buma' )
					],
					[
						'name'  => __( 'Form CTA', 'Buma' ),
						'id'    => '_menu-formcta',
						'type'  => 'text',
						'std'   => __( 'Request a quote', 'drivek' ),
						'title' => __( 'Form CTA', 'Buma' ),
						'desc'  => __( 'Insert the CTA for the form', 'Buma' )
					],
					[
						'name'  => __( 'Service Field Value', 'Buma' ),
						'id'    => '_menu-servicevalue',
						'type'  => 'text',
						'std'   => '',
						'title' => __( 'Service Field Value', 'Buma' ),
						'desc'  => __( 'Insert the value for the hidden field "service"', 'Buma' )
					],
				]
			]
		];

		MetaBox::create()->add_config( $config );

		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 *
	 * @return static
	 */
	public function register_post_type(): CptInterface {
		$labels = [
			'name'               => _x( 'Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'singular_name'      => _x( 'Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'menu_name'          => _x( 'Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'name_admin_bar'     => _x( 'Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'parent_item_colon'  => _x( 'Parent Item:', 'Menu Restaurant CPT', 'Buma' ),
			'all_items'          => _x( 'All Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'add_new_item'       => _x( 'Add New Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'add_new'            => _x( 'Add New Menu', 'Menu Restaurant CPT', 'Buma' ),
			'new_item'           => _x( 'New Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'edit_item'          => _x( 'Edit Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'update_item'        => _x( 'Update Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'view_item'          => _x( 'View Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'search_items'       => _x( 'Search Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'not_found'          => _x( 'Not found', 'Menu Restaurant CPT', 'Buma' ),
			'not_found_in_trash' => _x( 'Not found in Trash', 'Menu Restaurant CPT', 'Buma' ),
		];
		$args   = [
			'label'               => _x( 'Menu Restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'description'         => _x( 'List menus of restaurant', 'Menu Restaurant CPT', 'Buma' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail', 'custom-fields', 'page-attributes', ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-media-text',
			'rewrite'             => [
				'slug' => 'menu'
			],
			'taxonomies' => [
				'category'
			],
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		];
		register_post_type( 'menu_page', $args );

		return $this;
	}

	public function my_action_javascript() {
		echo '<script type="text/javascript">

			  </script>';
	}
}
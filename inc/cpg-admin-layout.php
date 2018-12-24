<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Add column to media library
function cpg_add_palette_column( $columns ) {
    $columns['cpg_dominant_color_column'] = __( 'Color Palette', 'cpg' );
    return $columns;
}
add_filter( 'manage_media_columns', 'cpg_add_palette_column' );

//Make column sortable
function cpg_make_sortable_column($columns){
  $columns['cpg_dominant_color_column'] = 'cpg_dominant_color_column';
  return $columns;
}
add_filter( 'manage_upload_sortable_columns', 'cpg_make_sortable_column' );

//Add content to column
function cpg_set_palette_column_content( $column_name, $post_id ) {
    if ( $column_name == 'cpg_dominant_color_column' ) {
    	$colors = get_the_terms( $post_id, 'cpg_dominant_color' );
        if( $colors ) {
        	$dominant = $colors[0];
        	$dominant = $dominant->name;
        	$palette = get_the_terms( $post_id, 'cpg_palette' );

        	$output = cpg_admin_show_palette( $dominant, $palette, $post_id );
        }else{
        	$output = cpg_admin_no_palette( $post_id );
        }
    	echo $output;
    }
}
add_action( 'manage_media_custom_column', 'cpg_set_palette_column_content', 10, 2 );

//Load scripts only when on media library
function cpg_load_custom_wp_admin_scripts( $hook ) {
    wp_register_style(
    	'cpg-generate-palette-column-css',
    	plugins_url( 'assets/css/admin-column-styles.css', dirname(__FILE__) )
	);
	wp_register_script(
		'cpg-color-thief',
		plugins_url( 'assets/dependencies/color-thief/src/color-thief.js', dirname(__FILE__) )
	);
	wp_register_script(
		'cpg-admin-functions',
		plugins_url( 'assets/js/admin-functions.js', dirname(__FILE__) ),
		array( 'jquery' ),
		CPG_VERSION
	);
    wp_register_script(
    	'cpg-generate-palette',
    	plugins_url( 'assets/js/generate-palette.js', dirname(__FILE__) ),
    	array( 'jquery', 'cpg-color-thief', 'cpg-admin-functions' ),
		CPG_VERSION
	);
    wp_register_script(
    	'cpg-generate-palette-upload',
    	plugins_url( 'assets/js/generate-palette-upload.js', dirname(__FILE__) ),
    	array( 'jquery', 'cpg-color-thief' ),
		CPG_VERSION
	);
    wp_register_script(
    	'cpg-bulk-generate-palette',
    	plugins_url( 'assets/js/bulk-generate-palette.js', dirname(__FILE__) ),
    	array( 'jquery', 'iris', 'cpg-color-thief', 'cpg-admin-functions' ),
		CPG_VERSION
	);
    wp_register_style(
    	'cpg-generate-palette-settings-page-css',
    	plugins_url( 'assets/css/admin-settings-page-styles.css', dirname(__FILE__) ),
    	array(),
    	CPG_VERSION
	);
    wp_register_style(
    	'cpg-media-popup-style-css',
    	plugins_url( 'assets/css/admin-media-popup-styles.css', dirname(__FILE__) ),
    	array(),
    	CPG_VERSION
	);

	wp_localize_script( 'cpg-bulk-generate-palette', 'cpg',
		array(
			'generating' => __( 'Generating', 'cpg' ),
			'deleting' => __('Deleting', 'cpg'),
			'regenerating' => __('Regenerating', 'cpg'),
			'done' => __( 'All images have a palette. Well done!', 'cpg' ),
			'enter_value' => __('Please enter a name for this color row', 'cpg'),
			'enter_value_placeholder' => __('Color name', 'cpg'),
			'keep_open' => __('Keep this page open until everything is done', 'cpg'),
			'loading_failed' => __('Attachment skipped - ID'),
			'removed' => __('All palettes are successfully removed', 'cpg')
		)
	);

    wp_localize_script( 'cpg-admin-functions', 'cpg',
		array(
			'error' => __( 'Something went wrong', 'cpg' ),
			'error_0' => __('Not connected, please verify your network', 'cpg'),
			'error_1' => __('Requested page not found. (404)', 'cpg'),
			'error_2' => __( 'Internal Server Error (500)', 'cpg' ),
			'error_3' => __( 'Requested JSON parse failed', 'cpg' ),
			'error_4' => __( 'Time out error', 'cpg' ),
			'error_5' => __( 'Ajax request aborted', 'cpg' ),
			'error_x' => __( 'Uncaught error', 'cpg' ),
		)
	);

	wp_localize_script( 'cpg-generate-palette', 'cpg',
		array(
			'generating' => __( 'Generating', 'cpg' ),
			'trashing' => __( 'Trashing', 'cpg' ),
			'confirm_trash' => __( 'You\'re about to permanently delete this palette.', 'cpg') . "\n" . __('\'Cancel\' to stop, \'OK\' to delete!', 'cpg')
		)
	);

	$options = get_option('cpg_options');
	$colors = isset( $options['colors'] ) ? $options['colors'] : 10;
	$thumb_w = get_option( 'thumbnail_size_w' );
	$thumb_h = get_option( 'thumbnail_size_h' );
	wp_localize_script( 'cpg-generate-palette-upload', 'cpg_upload',
		array(
			'colors' => $colors,
			'_wpnonce' => wp_create_nonce( 'cpg_add_palette_on_upload_nonce' ),
			'thumb' => '-' . $thumb_w . 'x' . $thumb_h
		)
	);

	wp_enqueue_script( 'cpg-generate-palette-upload' );

	switch($hook){
		case 'upload.php':
		    wp_enqueue_script( 'cpg-admin-functions' );
		    wp_enqueue_script( 'cpg-generate-palette' );
		    wp_enqueue_style( 'cpg-generate-palette-column-css' );
			break;

		case 'media_page_color-palette-generator':
		    wp_enqueue_script( 'cpg-color-thief' );
		    wp_enqueue_script( 'cpg-admin-functions' );
		    wp_enqueue_script( 'cpg-bulk-generate-palette' );
		    wp_enqueue_style( 'cpg-generate-palette-settings-page-css' );
		    wp_enqueue_style( 'wp-color-picker' );
			break;

		case 'post.php':
		    wp_enqueue_style( 'cpg-media-popup-style-css' );
		default:
			return;
			break;
	}
}
add_action( 'admin_enqueue_scripts', 'cpg_load_custom_wp_admin_scripts' );

function cpg_enqueue_scripts() {
	if( !is_admin() ) {

		wp_register_style(
	    	'cpg-frontend-styles-css',
	    	plugins_url( 'assets/css/cpg-frontend-styles.css', dirname(__FILE__) )
		);
	    wp_enqueue_style( 'cpg-frontend-styles-css' );

		wp_register_style(
	    	'cpg-frontend-widget-styles-css',
	    	plugins_url( 'assets/css/cpg-frontend-widget-styles.css', dirname(__FILE__) )
		);
    	wp_enqueue_style( 'cpg-frontend-widget-styles-css' );
		wp_register_style(
	    	'cpg-frontend-search-page-styles-css',
	    	plugins_url( 'assets/css/cpg-frontend-search-page-styles.css', dirname(__FILE__) )
		);

		if( is_search() ){
			global $wp_query;
			$check_search = explode('/', $wp_query->query['s']);
			if( get_query_var('color') != "" || $check_search[0] == 'color' ){
				wp_enqueue_style( 'cpg-frontend-search-page-styles-css' );
			}
		}

	}
}
add_action( 'wp_enqueue_scripts', 'cpg_enqueue_scripts' );

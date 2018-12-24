<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Make it possible to add palette per image
add_action( 'wp_ajax_cpg_add_palette', 'cpg_add_palette' );
function cpg_add_palette() {
	if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : "";
		$dominant = isset( $_POST['dominant'] ) ? sanitize_text_field( $_POST['dominant'] ) : "";
		$palette = isset( $_POST['palette'] ) ? array_map( 'sanitize_text_field', $_POST['palette'] ) : "";
		$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : "" ;
		$options = get_option('cpg_options');
		$autogenerate = isset( $options['autogenerate'] ) ? $options['autogenerate'] : false;

		if( wp_verify_nonce( $nonce, 'cpg_add_palette_on_upload_nonce') &&
			empty( $post_id ) &&
			isset($_POST['file'])  &&
			!empty($_POST['file'])
		) {
			$post_id = attachment_url_to_postid( $_POST['file'] );
			if( empty( $post_id ) ) {
				$output =  __( 'No file found', 'cpg');
				echo json_encode( $output );
   	 			wp_die();
			}
		}

		if( wp_verify_nonce( $nonce, 'cpg_add_palette_on_upload_nonce') && !$autogenerate ){
			$output =  __( 'Auto generation of palettes disabled', 'cpg');
			echo json_encode( $output );
		}elseif(
			get_post_type( $post_id ) == 'attachment' &&
			(
				wp_verify_nonce( $nonce, 'cpg_add_palette_'.$post_id.'_nonce' ) ||
				wp_verify_nonce( $nonce, 'cpg_add_palette_on_upload_nonce')
			)
		){
		 	wp_cache_flush();
			wp_defer_term_counting( true );
			$term_exists = term_exists($dominant, 'cpg_dominant_color');
			if ($term_exists === 0 || $term_exists === null) {
				$PKR = new PKRoundColor();
				$parent = $PKR->getRoundedColor($dominant);
				$term_parent = get_term_by( 'slug', $parent, 'cpg_dominant_color' );
				wp_insert_term( $dominant, 'cpg_dominant_color', array( 'parent' => $term_parent->term_id ) );
			}

			wp_set_object_terms( $post_id, $dominant, 'cpg_dominant_color', true );
			wp_set_object_terms( $post_id, $palette, 'cpg_palette' );
			$output = cpg_admin_show_palette( $dominant, $palette, $post_id );
			wp_defer_term_counting( false );
			echo json_encode( $output );
		}else{
			$output = '<span style="color:#f00;">' . __( 'Something went wrong', 'cpg' ) . '</span>';
			echo json_encode( $output );
		}
	}
    wp_die();
}

//Make it possible to remove palette references per image
add_action( 'wp_ajax_cpg_trash_palette', 'cpg_trash_palette' );
function cpg_trash_palette() {
	if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : "";
		$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : "" ;

		if(
			get_post_type( $post_id ) == 'attachment' &&
			wp_verify_nonce( $nonce, 'cpg_remove_palette_'.$post_id.'_nonce' )
		) {
			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );
			wp_delete_object_term_relationships( $post_id, 'cpg_dominant_color' );
			wp_delete_object_term_relationships( $post_id, 'cpg_palette' );
			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			$output = cpg_admin_no_palette( $post_id );
			echo json_encode( $output );
		}else{
			$output = '<span style="color:#f00;">' . __( 'Something went wrong', 'cpg' ) . '</span>';
			echo json_encode( $output );
		}

	}
    wp_die();
}


add_action( 'wp_ajax_cpg_exclude_bulk', 'cpg_exclude_bulk' );
function cpg_exclude_bulk(){
	if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : "";
		$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : "" ;

		if(
			get_post_type( $post_id ) == 'attachment' &&
			wp_verify_nonce( $nonce, 'cpg_bulk_generate_palette_'.$post_id.'_nonce' )
		) {
			add_post_meta($post_id, 'cpg_exclude', 'true');

			$next_img = get_attachment_without_colors( $post_id );
			if( is_array( $next_img ) ) {
				$nonce = array(
					'nonce' => wp_create_nonce( 'cpg_bulk_generate_palette_'.$next_img['id'].'_nonce' ),
					'regenerate' => false
				);
				$result = array_merge( $next_img, $nonce );
			}else{
				$result = array( 'more' => false );
			}
			echo json_encode( $result );
		}else{
			$output = '<span style="color:#f00;">' . __( 'Something went wrong', 'cpg' ) . '</span>';
			echo json_encode( $output );
		}

	}
    wp_die();
}

//Make it possible to bulk add a palette per image
add_action( 'wp_ajax_cpg_bulk_add_palette', 'cpg_bulk_add_palette' );
function cpg_bulk_add_palette() {
	if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : "";
		$dominant = isset( $_POST['dominant'] ) ? sanitize_text_field( $_POST['dominant'] ) : "";
		$palette = isset( $_POST['palette'] ) ? array_map( 'sanitize_text_field', $_POST['palette'] ) : "";
		$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : "" ;
		$regenerate = isset( $_POST['regenerate'] ) ? wp_validate_boolean( $_POST['regenerate'] ) : false;

		if( $regenerate === 'reset' ){
			cpg_setup_taxonomies( false, true );
			$result = array(
				'more' => false,
				'reset' => true
			);
		}elseif( $regenerate == true && wp_verify_nonce( $nonce, 'cpg_bulk_regenerate_palette_nonce' ) ) {
			cpg_setup_taxonomies( true, false );
			$next_img = get_attachment_without_colors();
			if( is_array( $next_img ) ) {
				$nonce = array(
					'nonce' => wp_create_nonce( 'cpg_bulk_generate_palette_'.$next_img['id'].'_nonce' ),
					'regenerate' => true
				);
				$result = array_merge( $next_img, $nonce );
			}else{
				$result = array( 'more' => false );
			}
			echo json_encode( $result );
		}else{
			if(
				get_post_type( $post_id ) == 'attachment' &&
				wp_verify_nonce( $nonce, 'cpg_bulk_generate_palette_'.$post_id.'_nonce' )
			) {
		 		wp_cache_flush();
				wp_defer_term_counting( true );
				$term_exists = term_exists($dominant, 'cpg_dominant_color');
				if ($term_exists === 0 || $term_exists === null) {
					$PKR = new PKRoundColor();
					$parent = $PKR->getRoundedColor($dominant);
					$term_parent = get_term_by( 'slug', $parent, 'cpg_dominant_color' );
					wp_insert_term( $dominant, 'cpg_dominant_color', array( 'parent' => $term_parent->term_id ) );
				}

				wp_set_object_terms( $post_id, $dominant, 'cpg_dominant_color', true );
				wp_set_object_terms( $post_id, $palette, 'cpg_palette' );

				$next_img = get_attachment_without_colors( $post_id );
				if( is_array( $next_img ) ) {
					$nonce = array(
						'nonce' => wp_create_nonce( 'cpg_bulk_generate_palette_'.$next_img['id'].'_nonce' ),
						'regenerate' => false
					);
					$result = array_merge( $next_img, $nonce );
				}else{
					$result = array( 'more' => false );
				}
				echo json_encode( $result );
			}else{
				$output = '<span style="color:#f00;">' . __( 'Something went wrong', 'cpg' ) . '</span>';
				echo json_encode( $output );
			}
		}

	}
    wp_die();
}

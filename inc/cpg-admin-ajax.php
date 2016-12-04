<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Make it possible to add palette per image
add_action( 'wp_ajax_cpg_add_palette', 'cpg_add_palette' );
function cpg_add_palette() {
	if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : "";
		$dominant = isset( $_POST['dominant'] ) ? $_POST['dominant'] : "";
		$palette = isset( $_POST['palette'] ) ? $_POST['palette'] : "";
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : "" ;

		$PKR = new PKRoundColor();
		$parent = $PKR->getRoundedColor($dominant);

		if(
			get_post_type( $post_id ) == 'attachment' &&
			wp_verify_nonce( $nonce, 'cpg_add_palette_'.$post_id.'_nonce' )
		){
			wp_set_object_terms( $post_id, $dominant, 'cpg_dominant_color', true );

			$term_parent = get_term_by( 'slug', $parent, 'cpg_dominant_color' );
			$term_child  = get_term_by( 'slug', $dominant, 'cpg_dominant_color' );
			wp_update_term( $term_child->term_id, 'cpg_dominant_color', array( 'parent' => $term_parent->term_id ) );

			wp_set_object_terms( $post_id, $palette, 'cpg_palette' );

			$output = cpg_admin_show_palette( $dominant, $palette, $post_id );
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
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : "" ;

		if(
			get_post_type( $post_id ) == 'attachment' &&
			wp_verify_nonce( $nonce, 'cpg_remove_palette_'.$post_id.'_nonce' )
		) {
			wp_delete_object_term_relationships( $post_id, 'cpg_dominant_color' );
			wp_delete_object_term_relationships( $post_id, 'cpg_palette' );

			$output = cpg_admin_no_palette( $post_id );
			echo json_encode( $output );
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
		$dominant = isset( $_POST['dominant'] ) ? $_POST['dominant'] : "";
		$palette = isset( $_POST['palette'] ) ? $_POST['palette'] : "";
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : "" ;
		$regenerate = isset( $_POST['regenerate'] ) ? $_POST['regenerate'] : false;

		if( $regenerate == true && wp_verify_nonce( $nonce, 'cpg_bulk_regenerate_palette_nonce' ) ) {
			cpg_setup_taxonomies( true );
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
			$PKR = new PKRoundColor();
			$parent = $PKR->getRoundedColor($dominant);

			if(
				get_post_type( $post_id ) == 'attachment' &&
				wp_verify_nonce( $nonce, 'cpg_bulk_generate_palette_'.$post_id.'_nonce' )
			) {
				wp_set_object_terms( $post_id, $dominant, 'cpg_dominant_color' );

				$term_parent = get_term_by( 'slug', $parent, 'cpg_dominant_color' );
				$term_child  = get_term_by( 'slug', $dominant, 'cpg_dominant_color' );
				wp_update_term( $term_child->term_id, 'cpg_dominant_color', array( 'parent' => $term_parent->term_id ) );

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

//Make it possible for users to edit the color table
add_action( 'wp_ajax_cpg_edit_color_table', 'cpg_edit_color_table' );
function cpg_edit_color_table() {
	if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$options = get_option('cpg_options');
		$option_to_update = $options['color_table'];
		$color_table = isset( $_POST['color_table'] ) ? $_POST['color_table'] : "";
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : "" ;

		if(
			$color_table != '' &&
			wp_verify_nonce( $nonce, 'cpg_update_color_table_'.$post_id.'_nonce' )
		) {
			var_dump($color_table);
			echo json_encode( $output );
		}else{
			$output = '<span style="color:#f00;">' . __( 'Something went wrong', 'cpg' ) . '</span>';
			echo json_encode( $output );
		}

	}
    wp_die();
}

<?php
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	//Output if palette is available
	function cpg_admin_show_palette( $dominant, $palette, $post_id ){
		$response = '<div class="cpg__dominant-color cpg__color-item" style="background-color:'.$dominant.';" data-title="Dominant: '.$dominant.'"></div>';

		if( $palette ){
			sort( $palette );
			$response .= '<ul class="cpg__palette-list">';
			foreach ( $palette as $color ) {
				if( is_object( $color ) ){
					$color = $color->name;
				}
				$response .= '<li class="cpg__palette-item cpg__color-item" style="background-color:'.$color.';" data-title="'.$color.'"></li>';
			}
			$response .= '</ul>';
		}

		$response .= '<div class="row-actions">
				<span class="trash">
					<a href="' . get_admin_url( null, 'upload.php?page='.CPG_NAME ) . '&action=cpg_trash_palette&post_id='.$post_id.'&_wpnonce='.wp_create_nonce( 'cpg_remove_palette_'.$post_id.'_nonce' ).'"  class="cpg-button-palette-trash">
						'.__( 'Trash palette', 'cpg' ).'
					</a>
				</span>
			</div>';

		return $response;
	}

	//Output if palette is not yet generated
	function cpg_admin_no_palette( $post_id ){
		$img = wp_get_attachment_image_src( $post_id, 'large' );
    	$img = $img[0];
		$options = get_option('cpg_options');
		$colors = isset( $options['colors'] ) ? $options['colors'] : 10;
    	if( wp_attachment_is_image( $post_id ) && file_exists( get_attached_file( $post_id ) ) ) {
    		$response = '<a
					href="' . get_admin_url( null, 'upload.php?page='.CPG_NAME ) . '&action=cpg_add_palette&post_id='.$post_id.'&_wpnonce='.wp_create_nonce( 'cpg_add_palette_'.$post_id.'_nonce' ).'&colors='.$colors.'"
					class="button cpg-button-palette" data-src="'.$img.'">
					'.__( 'Generate Palette', 'cpg' ).'
				</a>';
    	}elseif( file_exists( get_attached_file( $post_id ) ) ) {
    		$response = '<span>—</span>';
    	}else{
    		$response = '<span style="color: #F00;">' . __( 'File not found', 'cpg' ) . '</span>';
    	}

		return $response;
    }

    //Color array used for filtering
    function cpg_return_colors(){

		$colors = array(
	        'red' =>		'f44336',
	        'pink' =>		'e91e63',
	        'purple' =>		'9c27b0',
	        'deep-purple' =>'673ab7',
	        'indigo' =>		'3f51b5',
	        'blue' =>		'2196f3',
	        'cyan' =>		'00bcd4',
	        'teal' =>		'009688',
	        'green' =>		'4caf50',
	        'light-green' =>'8bc34a',
	        'lime' =>		'cddc39',
	        'yellow' =>		'ffeb3b',
	        'amber' =>		'ffc107',
	        'orange' =>		'ff9800',
	        'deep-orange' =>'ff5722',
	        'brown' =>		'795548',
	        'grey' =>		'9e9e9e',
	        'blue-grey' =>	'607d8b',
	        'black' =>		'000000',
	        'white' =>		'ffffff'
	    );


    	return $colors;
	}

	//Show # of images with a palette
	function cpg_img_count( $palette = false ){
        $query_img_args = array(
	        'post_type' => 'attachment',
	        'post_mime_type' =>array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
            ),
	        'post_status' => 'inherit',
	        'posts_per_page' => -1,
        );

        if( $palette ){
        	$query_img_args['tax_query'][] = array(
	            'taxonomy' 	=> 'cpg_dominant_color',
	            'terms'    => get_terms( 'cpg_dominant_color', array( 'fields' => 'ids'  ) ),
	            'operator'	=> 'IN'
	        );
        }
        $query_img = new WP_Query( $query_img_args );
        return $query_img->post_count;
	}

	//return first image which needs a palette (used for bulk generating)
	function get_attachment_without_colors( $post_id = false ){
        $query_img_args = array(
	        'post_type' => 'attachment',
	        'post_mime_type' => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
            ),
	        'post_status' => 'inherit',
	        'posts_per_page' => 1,
            'fields' => 'ids',
	        'tax_query' => array(
	        	array(
		            'taxonomy' 	=> 'cpg_dominant_color',
		            'terms'    => get_terms( 'cpg_dominant_color', array( 'fields' => 'ids'  ) ),
		            'operator'	=> 'NOT IN'
		        )
	        ),
        );

        if( $post_id ){
	        $query_img_args['post__not_in'] = array( $post_id );
        }

        $next_post_id = get_posts( $query_img_args );
        $next_post_id = $next_post_id[0];
        $next_post_src = wp_get_attachment_image_src( $next_post_id, 'large' );
        if( $next_post_src ){
        	$next_post_src = $next_post_src[0];
        }

        if( $next_post_id ){
        	$result = array(
        		'more' => true,
        		'id' => $next_post_id,
        		'src' => $next_post_src
        	);
        }else{
        	$result = "";
        }

        return $result;
	}

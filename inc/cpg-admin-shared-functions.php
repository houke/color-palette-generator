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
    function cpg_return_colors($name = false){

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

		if($name){
    		return $colors[$name];
		}else{
    		return $colors;
		}
	}

	function cpg_return_tints($color){
		$colors = array(
			'red' 			=> array('ffcdd2', 'ef9a9a', 'e57373', 'ef5350', 'e53935', 'd32f2f', 'c62828', 'b71c1c'),
        	'pink' 			=> array('f8bbd0', 'f48fb1', 'f06292', 'ec407a', 'd81b60', 'c2185b', 'aD1457', '880e4f'),
	        'purple' 		=> array('e1bee7', 'ce93D8', 'ba68c8', 'ab47bc', '8e24aa', '7b1fa2', '6a1b9a', '4a148c'),
        	'deep-purple' 	=> array('d1c4e9', 'b39ddb', '9575cd', '7e57c2', '5e35b1', '512da8', '4527a0', '311b92'),
       	 	'indigo' 		=> array('c5cae9', '9fa8da', '7986cb', '5c6bc0', '3949ab', '303f9f', '283593', '1a237e'),
        	'blue'			=> array('bbdefb', '90caf9', '64b5f6', '42a5f5', '1e88e5', '1976d2', '1565c0', '0d47a1'),
        	'cyan' 			=> array('b2ebf2', '80deea', '4dd0e1', '26c6da', '00acc1', '0097a7', '00838f', '006064'),
	        'teal' 			=> array('b2dfdb', '80cbc4', '4db6ac', '26a69a', '00897b', '00796b', '00695c', '004d40'),
	        'green'			=> array('c8e6c9', 'a5d6a7', '81c784', '66bb6a', '43a047', '388e3c', '2e7d32', '1b5e20'),
	        'light-green'	=> array('dcedc8', 'c5e1a5', 'aed581', '9ccc65', '7cb342', '689f38', '558b2f', '33691e'),
	        'lime'			=> array('f0f4c3', 'e6ee9c', 'dce775', 'd4e157', 'c0ca33', 'afb42b', '9e9d24', '827717'),
	        'yellow'		=> array('fff9c4', 'fff59d', 'fff176', 'ffee58', 'fdd835', 'fbc02d', 'f9a825', 'f57f17'),
	        'amber' 		=> array('ffecb3', 'ffe082', 'ffd54f', 'ffca28', 'ffb300', 'ffa000', 'ff8f00', 'ff6f00'),
	        'orange'		=> array('ffe0b2', 'ffcc80', 'ffb74d', 'ffa726', 'fb8c00', 'f57c00', 'ef6c00', 'e65100'),
	        'deep-orange'	=> array('ffccbc', 'ffab91', 'ff8a65', 'ff7043', 'f4511e', 'e64a19', 'd84315', 'bf360c'),
	        'brown' 		=> array('d7ccc8', 'bcaaa4', 'a1887f', '8d6e63', '6d4c41', '5d4037', '4e342e', '3e2723'),
	        'grey'			=> array('f5f5f5', 'eeeeee', 'e0e0e0', 'bdbdbd', '757575', '616161', '424242', '212121'),
	        'blue-grey'		=> array('cfd8dc', 'b0bec5', '90a4ae', '78909c', '546e7a', '455a64', '37474f', '263238'),
	        'black' 		=> array(),
	        'white'			=> array('ffebee', 'fce4ec', 'f3e5f5', 'ede7f6', 'e8eAf6', 'e3f2fd', 'e1f5fe', 'e0f7fA', 'e0f2f1', 'e8f5e9', 'f1f8e9', 'f9fbe7', 'fffde7', 'fff8e1', 'fff3e0', 'fbe9e7', 'efebe9', 'fAfAfA', 'eceff1')
        );

		if( array_key_exists($color, $colors) ){
	    	return $colors[$color];
	    }else{
	        foreach ($colors as $i=>$c) {
	        	if( in_array($color, $c) ){
	        		return cpg_return_colors($i);
	        		break;
	        	}
	        }
    	}
	}

	function searchItemsByKey($array, $key){
	   $results = array();

	  if (is_array($array))
	  {
	    if (isset($array[$key]) && key($array)==$key)
	        $results[] = $array[$key];

	    foreach ($array as $sub_array)
	        $results = array_merge($results, searchItemsByKey($sub_array, $key));
	  }

	 return  $results;
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

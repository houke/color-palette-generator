<?php
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	//Output if palette is available
	function cpg_admin_show_palette( $dominant, $palette, $post_id ){
		$response = '<div class="cpg__dominant-color cpg__color-item" style="background-color:'.$dominant.';" data-title="Dominant: '.$dominant.'"></div>';
		
		if( $palette ){
			$options = get_option('cpg_options');
			$order = isset( $options['order'] ) ? $options['order'] : 'rand';
			if( $order == 'rand' ){
				shuffle( $palette );
			}elseif( $order == 'name|asc'){
				usort($palette, "cpg_sort");
			}else{
				usort($palette, "cpg_sort");
				$palette = array_reverse($palette);
			}
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

	function cpg_sort($a, $b){
	   	return strcmp($a->slug, $b->slug);
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
    function cpg_return_colors($key = false){
		$options = get_option('cpg_options');
    	$searchcolors = isset($options['color_table']) ? $options['color_table'] : cpg_default_color_table();

    	$colors = array();
    	if( $searchcolors && is_array($searchcolors) && !empty($searchcolors) ) {
			foreach ($searchcolors as $name => $code) {
				if( isset( $code['code'] ) ){
		    		$colors[$name] = str_replace('#', '', $code['code']);
		    	}
		    }
	    }

		if($key){
    		return $colors[$key];
		}else{
    		return $colors;
		}
	}

	function cpg_return_tints($color){
		$options = get_option('cpg_options');
    	$searchcolors = isset($options['color_table']) ? $options['color_table'] : cpg_default_color_table();

    	$colors = array();
    	if( $searchcolors && is_array($searchcolors) && !empty($searchcolors) ) {
			foreach ($searchcolors as $name => $code) {
				$tints = isset( $code['tints'] ) ? $code['tints'] : array();
				array_push($tints, $code['code']);
		    	$colors[$name] = str_replace('#', '', $tints);
		    }
		}

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

	function cpg_default_color_table(){
		return array(
		  	'red' => array(
			    'code' => '#f44336',
			    'name' => 'Red',
			    'tints' => array(
			      	0 => '#ffcdd2',
			      	1 => '#ef9a9a',
			      	2 => '#e57373',
			      	3 => '#ef5350',
			      	4 => '#e53935',
			      	5 => '#d32f2f',
			      	6 => '#c62828',
			      	7 => '#b71c1c'
			    )
		  	),
		  	'pink' => array(
			    'code' => '#e91e63',
			    'name' => 'Pink',
			    'tints' => array(
			      	0 => '#f8bbd0',
			      	1 => '#f48fb1',
			      	2 => '#f06292',
			      	3 => '#ec407a',
			      	4 => '#d81b60',
			      	5 => '#c2185b',
			      	6 => '#ad1457',
			      	7 => '#880e4f'
			    )
		  	),
		  	'purple' => array(
			    'code' => '#9c27b0',
			    'name' => 'Purple',
			    'tints' => array(
			      	0 => '#e1bee7',
			      	1 => '#ce93d8',
			      	2 => '#ba68c8',
			      	3 => '#ab47bc',
			      	4 => '#8e24aa',
			      	5 => '#7b1fa2',
			      	6 => '#6a1b9a',
			      	7 => '#4a148c'
			    )
		  	),
		  	'deep-purple' => array(
			    'code' => '#673ab7',
			    'name' => 'Deep Purple',
			    'tints' =>  array(
			      	0 => '#d1c4e9',
			      	1 => '#b39ddb',
			      	2 => '#9575cd',
			      	3 => '#7e57c2',
			      	4 => '#5e35b1',
			      	5 => '#512da8',
			      	6 => '#4527a0',
			      	7 => '#311b92'
			    )
		  	),
		  	'indigo' => array(
			    'code' => '#3f51b5',
			    'name' => 'Indigo',
			    'tints' => array(
			      	0 => '#c5cae9',
			      	1 => '#9fa8da',
			      	2 => '#7986cb',
			      	3 => '#5c6bc0',
			      	4 => '#3949ab',
			      	5 => '#303f9f',
			      	6 => '#283593',
			      	7 => '#1a237e'
			    )
		  	),
		  	'blue' => array(
			    'code' => '#2196f3',
			    'name' => 'Blue',
			    'tints' => array(
			      	0 => '#bbdefb',
			      	1 => '#90caf9',
			      	2 => '#64b5f6',
			      	3 => '#42a5f5',
			      	4 => '#1e88e5',
			      	5 => '#1976d2',
			      	6 => '#1565c0',
			      	7 => '#0d47a1'
			    )
		  	),
		  	'cyan' => array(
			    'code' => '#00bcd4',
			    'name' => 'Cyan',
			    'tints' => array(
			      	0 => '#b2ebf2',
			      	1 => '#80deea',
			      	2 => '#4dd0e1',
			      	3 => '#26c6da',
			      	4 => '#00acc1',
			      	5 => '#0097a7',
			      	6 => '#00838f',
			      	7 => '#006064'
			    )
		  	),
		  	'teal' => array(
			    'code' => '#009688',
			    'name' => 'Teal',
			    'tints' => array(
			      	0 => '#b2dfdb',
			      	1 => '#80cbc4',
			      	2 => '#4db6ac',
			      	3 => '#26a69a',
			      	4 => '#00897b',
			      	5 => '#00796b',
			      	6 => '#00695c',
			      	7 => '#004d40'
			    )
		  	),
		  	'green' => array(
			    'code' => '#4caf50',
			    'name' => 'Green',
			    'tints' => array(
			      	0 => '#c8e6c9',
			      	1 => '#a5d6a7',
			      	2 => '#81c784',
			      	3 => '#66bb6a',
			      	4 => '#43a047',
			      	5 => '#388e3c',
			      	6 => '#2e7d32',
			      	7 => '#1b5e20'
			    )
		  	),
		  	'light-green' => array(
			    'code' => '#8bc34a',
			    'name' => 'Light Green',
			    'tints' => array(
			      	0 => '#dcedc8',
			      	1 => '#c5e1a5',
			      	2 => '#aed581',
			      	3 => '#9ccc65',
			      	4 => '#7cb342',
			      	5 => '#689f38',
			      	6 => '#558b2f',
			      	7 => '#33691e'
			    )
		  	),
		  	'lime' => array(
			    'code' => '#cddc39',
			    'name' => 'Lime',
			    'tints' => array(
			      	0 => '#f0f4c3',
			      	1 => '#e6ee9c',
			      	2 => '#dce775',
			      	3 => '#d4e157',
			      	4 => '#c0ca33',
			      	5 => '#afb42b',
			      	6 => '#9e9d24',
			      	7 => '#827717'
			    )
		  	),
		  	'yellow' => array(
			    'code' => '#ffeb3b',
			    'name' => 'Yellow',
			    'tints' => array(
			      	0 => '#fff9c4',
			      	1 => '#fff59d',
			      	2 => '#fff176',
			      	3 => '#ffee58',
			      	4 => '#fdd835',
			      	5 => '#fbc02d',
			      	6 => '#f9a825',
			      	7 => '#f57f17'
			    )
		  	),
		  	'amber' => array(
			    'code' => '#ffc107',
			    'name' => 'Amber',
			    'tints' => array(
			      	0 => '#ffecb3',
			      	1 => '#ffe082',
			      	2 => '#ffd54f',
			      	3 => '#ffca28',
			      	4 => '#ffb300',
			      	5 => '#ffa000',
			      	6 => '#ff8f00',
			      	7 => '#ff6f00'
			    )
		  	),
		  	'orange' => array(
			    'code' => '#ff9800',
			    'name' => 'Orange',
			    'tints' => array(
			      	0 => '#ffe0b2',
			      	1 => '#ffcc80',
			      	2 => '#ffb74d',
			      	3 => '#ffa726',
			      	4 => '#fb8c00',
			      	5 => '#f57c00',
			      	6 => '#ef6c00',
			      	7 => '#e65100'
			    )
		  	),
		  	'deep-orange' => array(
			    'code' => '#ff5722',
			    'name' => 'Deep Orange',
			    'tints' => array(
			      	0 => '#ffccbc',
			      	1 => '#ffab91',
			      	2 => '#ff8a65',
			      	3 => '#ff7043',
			      	4 => '#f4511e',
			      	5 => '#e64a19',
			      	6 => '#d84315',
			      	7 => '#bf360c'
			    )
		  	),
		  	'brown' => array(
			    'code' => '#795548',
			    'name' => 'Brown',
			    'tints' => array(
			      	0 => '#d7ccc8',
			      	1 => '#bcaaa4',
			      	2 => '#a1887f',
			      	3 => '#8d6e63',
			      	4 => '#6d4c41',
			      	5 => '#5d4037',
			      	6 => '#4e342e',
			      	7 => '#3e2723'
			    )
		  	),
		  	'grey' => array(
			    'code' => '#9e9e9e',
			    'name' => 'Grey',
			    'tints' => array(
			      	0 => '#f5f5f5',
			      	1 => '#eeeeee',
			      	2 => '#e0e0e0',
			      	3 => '#bdbdbd',
			      	4 => '#757575',
			      	5 => '#616161',
			      	6 => '#424242',
			    )
		  	),
		  	'blue-grey' => array(
			    'code' => '#607d8b',
			    'name' => 'Blue Grey',
			    'tints' => array(
			      	0 => '#cfd8dc',
			      	1 => '#b0bec5',
			      	2 => '#90a4ae',
			      	3 => '#78909c',
			      	4 => '#546e7a',
			      	5 => '#455a64',
			      	6 => '#37474f',
			    )
		  	),
		  	'black' => array(
			    'code' => '#000000',
			    'name' => 'Black',
			    'tints' => array(
			      	0 => '#111111',
			      	1 => '#222222',
			      	2 => '#263238'
			    )
		  	),
		  	'white' => array(
				'code' => '#ffffff',
				'name' => 'White',
				'tints' => array(
				  	0 => '#ffebee',
				  	1 => '#fce4ec',
				  	2 => '#f3e5f5',
				  	3 => '#ede7f6',
				  	4 => '#e8eaf6',
				  	5 => '#e3f2fd',
				  	6 => '#e1f5fe',
				  	7 => '#e0f7fa',
				  	8 => '#e0f2f1',
				  	9 => '#e8f5e9',
				  	10 => '#f1f8e9',
				  	11 => '#f9fbe7',
				  	12 => '#fffde7',
				  	13 => '#fff8e1',
				  	14 => '#fff3e0',
				  	15 => '#fbe9e7',
				  	16 => '#efebe9',
				  	17 => '#fafafa',
				  	18 => '#eceff1'
			    )
		  	)
		);
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
	function cpg_img_count( $type ){
		global $wpdb;

		if( $type == 'palette' ){
		    $querystr = "SELECT count(id)
		    	FROM $wpdb->posts p
		    	WHERE p.post_type = 'attachment'
		    	AND EXISTS (
		    		SELECT *
		    			FROM $wpdb->term_relationships rel
		    			JOIN $wpdb->term_taxonomy tax
		    			ON tax.term_taxonomy_id = rel.term_taxonomy_id
		    			AND tax.taxonomy = 'cpg_dominant_color'
		    			JOIN $wpdb->terms term
		    			ON term.term_id = tax.term_id
		    			WHERE p.ID = rel.object_id
		    		)";
			$result = $wpdb->get_var($querystr);
	    }elseif( $type == 'excluded' ){
	    	$querystr = "SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'cpg_exclude'";
			$result = $wpdb->get_results($querystr);
	    }else{
			$querystr = "SELECT count(id)
				FROM $wpdb->posts
				WHERE post_type = 'attachment'";
			$result = $wpdb->get_var($querystr);
	    }
		return $result;
	}

	//return first image which needs a palette (used for bulk generating)
	function get_attachment_without_colors( $post_id = false ){
		global $wpdb;
		$querystr = "SELECT ID
			FROM    $wpdb->posts p
			WHERE   p.post_type = 'attachment'
			        AND NOT EXISTS
			        (
			        SELECT  *
			        FROM    $wpdb->term_relationships rel
			        JOIN    $wpdb->term_taxonomy tax
			        ON      tax.term_taxonomy_id = rel.term_taxonomy_id
			                AND tax.taxonomy = 'cpg_dominant_color'
			        JOIN    $wpdb->terms term
			        ON      term.term_id = tax.term_id
			        WHERE   p.ID = rel.object_id
			        )
			        AND NOT EXISTS
			        (
					SELECT *
					FROM $wpdb->postmeta
					WHERE p.ID = $wpdb->postmeta.post_id
					AND meta_key = 'cpg_exclude'
			        )
			ORDER BY p.ID DESC
			LIMIT 1";
		$result = $wpdb->get_results($querystr);
		if(isset($result[0]) && isset($result[0]->ID)){
	        $next_post_id = $result[0]->ID;
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
	    }else{
        	$result = "";
	    }

        return $result;
	}

	function cpg_setup_taxonomies($regenerate = false, $reset = false){
		if($reset || $regenerate){
	        /** Delete All the Taxonomies */
	        global $wpdb;
	        $query = "
			  DELETE FROM $wpdb->postmeta
			  WHERE meta_key = 'cpg_exclude'
			";
			$wpdb->query($query);

			foreach ( array( 'cpg_dominant_color', 'cpg_palette' ) as $taxonomy ) {
				// Prepare & excecute SQL, Delete Terms
				$wpdb->get_results( $wpdb->prepare( "DELETE t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s')", $taxonomy ) );
				$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
			}
		}
		if( !$reset ){
		    $searchcolors = cpg_return_colors();
		    if( $searchcolors && is_array($searchcolors) && !empty($searchcolors) ) {
			    foreach ($searchcolors as $name => $code) {
			   		wp_insert_term( '#'.$code, 'cpg_dominant_color', array( 'slug' => $code, 'description' => $name ) );
			    }
			}
	    }
	}

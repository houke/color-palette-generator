<?php
	//add color query var
	function cpg_add_query_vars_color( $vars ){
	    $vars[] = 'color';
 	    return $vars;
	}
	add_filter( 'query_vars', 'cpg_add_query_vars_color' );

	function cpg_search_rewrite_rules( $search_rewrite_rules ){
	    global $wp_rewrite;
	    $wp_rewrite->add_rewrite_tag( '%color%', '([^/]+)', 'color=' );
	    $search_structure = $wp_rewrite->get_search_permastruct();
	    return $wp_rewrite->generate_rewrite_rules( $search_structure . '/color/%color%', EP_SEARCH );
	}
	add_filter( 'search_rewrite_rules', 'cpg_search_rewrite_rules' );

	function tah_create_new_url_querystring(){
	    add_rewrite_rule(
	        '^color/([^/]*)(/page/([0-9]+)?)?/?$',
	        'index.php?s=&color=$matches[1]&paged=$matches[3]',
	        'top'
	    );
	}
	add_action('init', 'tah_create_new_url_querystring');

	function search_filter( $query ) {
	 	if ( !is_admin() && $query->is_main_query() && $query->is_search ) {
 			$search = get_query_var( 's' );
 			$color = get_query_var( 'color' );
 			$colors = cpg_return_colors();
 			$check_search = explode('/', $search);

 			if(
				is_array($check_search) &&
				$check_search[0] == 'color' &&
				isset($colors[$check_search[1]])
			){
 				$search = "";
 				$color = $colors[$check_search[1]];
			}else{
				$color = $colors[$color];
			}
			$parent = get_term_by( 'slug', $color, 'cpg_dominant_color' );
			$childs = get_terms( 'cpg_dominant_color', array( 'hide_empty' => false, 'parent' => $parent->term_id ) );

			$colors = array();
			array_push($colors, $parent->slug);
			foreach ($childs as $child) {
				array_push($colors, $child->slug);
			}

 			if( !empty( $color ) ) {
	    		$query->set( 'post_status',  array( 'publish', 'inherit' ) );
	    		$query->set( 'post_type', 'attachment' );
	    		$query->set( 's', $search );
				$query->set( 'tax_query',
					array(
					    array(
					          'taxonomy' => 'cpg_dominant_color',
					          'field' => 'slug',
					          'terms' => $colors
					    )
					)
				);
 			}
	  	}
	  	return $query;
	}
	add_action('pre_get_posts','search_filter');

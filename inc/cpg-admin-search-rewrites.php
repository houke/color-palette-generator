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

	function search_filter( $query ) {
	 	if ( !is_admin() && $query->is_main_query() && $query->is_search ) {
 			$search = get_query_var( 's' );
 			$color = get_query_var( 'color' );

 			if( !empty( $color ) ) {
 				$color = str_replace( ',', '+', $color );
 				$color =  explode( '+', $color );
 				$colors = array();
	 			foreach ( $color as $c ) {
	 				if( is_array( cpg_get_tint_colors($c) ) ) {
	 					$colors = array_merge( $colors, cpg_get_tint_colors( $c ) );
	 				}
	 			}
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


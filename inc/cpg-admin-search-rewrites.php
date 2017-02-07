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

	function cpg_create_new_url_querystring(){
	    add_rewrite_rule(
	        '^color/([^/]*)(/page/([0-9]+)?)?/?$',
	        'index.php?s=&color=$matches[1]&paged=$matches[3]',
	        'top'
	    );
	}
	add_action('init', 'cpg_create_new_url_querystring');

	function cpg_search_filter( $query ) {
	 	if ( !is_admin() && $query->is_main_query() && $query->is_search ) {
 			$search = esc_html(get_query_var( 's' ));
 			$color = esc_html(get_query_var( 'color' ));
 			$colors = cpg_return_colors();
 			$check_search = explode('/', $search);
 			$cl = "";

 			if(
				is_array($check_search) &&
				$check_search[0] == 'color' &&
				isset($colors[$check_search[1]])
			){
 				$search = "";
 				$cl = $colors[$check_search[1]];
			}elseif( isset( $color ) && $color != "" && array_key_exists( $color, $colors ) ){
				$cl = $colors[$color];
			}

			if($cl != "" ){
				$parent = get_term_by( 'slug', $cl, 'cpg_dominant_color' );
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

	 			if( defined( 'WPSEO_VERSION' ) ) {
					add_filter( 'wpseo_title', 'cpg_filter_title' );
	 			} else {
	 				add_filter( 'pre_get_document_title', 'cpg_filter_title', 15 );
					add_filter( 'wp_title', 'cpg_filter_title', 15, 3 );
					add_filter( 'thematic_doctitle', 'cpg_filter_title', 15 );
	 			}

				add_filter( 'template_include', 'cpg_custom_search_tpl' );
	 		}
	  	}
	  	return $query;
	}
	add_action( 'pre_get_posts', 'cpg_search_filter' );

	function cpg_filter_title($title) {
		$search = esc_html(get_query_var( 's' ));
		$color = esc_html(get_query_var( 'color' ));
		$colors = cpg_return_colors();
		$check_search = explode('/', $search);

		if(
			is_array($check_search) &&
			$check_search[0] == 'color' &&
			isset($colors[$check_search[1]])
		){
			$title = __('Results for the color &lsquo;' . str_replace('-', ' ', $check_search[1]) . '&rsquo;', 'cpg');
		}elseif( isset( $color ) && $color != "" && array_key_exists( $color, $colors ) ){
			$title = __('Results for the color &lsquo;' . str_replace('-', ' ', $color) . '&rsquo;', 'cpg');
		} else{
			$title = $title;
		}
	    return $title;
	}

	function cpg_custom_search_tpl( $original_template ) {
		$priority_template_lookup = array(
			get_stylesheet_directory() . '/templates/search.php',
			get_template_directory() . '/templates/search.php',
			plugin_dir_path( dirname( __FILE__ ) ) . 'templates/search.php',
		);

		foreach ( $priority_template_lookup as $exists ) {
			if ( file_exists( $exists ) ) {
				return $exists;
				exit;
			}
		}
	    return $original_template;
	}

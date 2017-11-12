<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
//output palette after image
add_filter( 'prepend_attachment', 'cpg_prepend_attachment' );
function cpg_prepend_attachment( $attachment_content ){

	$options = get_option('cpg_options');
	$PKR = new PKRoundColor();

	if( isset( $options['show_on_attachment'] ) && $options['show_on_attachment'] == 'true' ){

		$output = wp_get_attachment_link( 0, 'large', false );
		$colors = get_the_terms( 0, 'cpg_dominant_color' );
		$palette = get_the_terms( 0, 'cpg_palette' );
		if( $colors ) {
			$dominant = $colors[0];
			$parent = get_term( $dominant->parent, 'cpg_dominant_color' );
	    	$dominant = $dominant->name;
	    	if( isset( $options['make_dominant_clickable']) && $options['make_dominant_clickable'] ){
	    		$output .= '<a href="'.esc_url( get_bloginfo( 'url' ).'/color/'.$parent->description.'/' ).'" class="cpg__dominant-color cpg__color-item" style="background-color:'.esc_attr($dominant).';" data-title="Dominant: '.esc_attr($dominant).'"></a>';
	    	}else{
	    		$output .= '<div class="cpg__dominant-color cpg__color-item" style="background-color:'.esc_attr($dominant).';" data-title="Dominant: '.esc_attr($dominant).'"></div>';
	    	}
	    }

		if( $palette ){
			$output .= '<ul class="cpg__palette-list">';
			shuffle($palette);
			foreach ( $palette as $color ) {
				if( is_object( $color ) ){
					$color = $color->name;
				}
				if( isset( $options['make_palette_clickable']) && $options['make_palette_clickable'] ){
					$parent = $PKR->getRoundedColor($color);
					$parent = get_term_by( 'slug', $parent, 'cpg_dominant_color' );

					$output .= '<li class="cpg__palette-item cpg__palette-item-helper"><a href="'.esc_url( get_bloginfo( 'url' ).'/color/'.$parent->description.'/' ).'" class="cpg__dominant-color cpg__color-item" style="background-color:'.esc_attr($color).';" data-title="'.esc_attr($color).'"></a></li>';
		    	}else{
		    		$output .= '<li class="cpg__palette-item cpg__color-item" style="background-color:'.esc_attr($color).';" data-title="'.esc_attr($color).'"></li>';
		    	}
			}
			$output .= '</ul>';
		}

	    // set the attachment image size to 'large'
		$attachment_content = sprintf( '<div class="cpg__palette-holder"><div class="cpg__palette-item">%s</div></div>', $output);
	}
    return $attachment_content;
}

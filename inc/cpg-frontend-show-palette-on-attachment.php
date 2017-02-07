<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
//output palette after image
add_filter( 'prepend_attachment', 'cpg_prepend_attachment' );
function cpg_prepend_attachment( $attachment_content ){

	$options = get_option('cpg_options');
	if( isset( $options['show_on_attachment'] ) && $options['show_on_attachment'] == 'true' ){
	    wp_enqueue_style( 'cpg-frontend-styles-css' );

		$output = wp_get_attachment_link( 0, 'large', false );
		$colors = get_the_terms( 0, 'cpg_dominant_color' );
		$palette = get_the_terms( 0, 'cpg_palette' );
		if( $colors ) {
			$dominant = $colors[0];
			$parent = get_term( $dominant->parent, 'cpg_dominant_color' );
	    	$dominant = $dominant->name;
	    	$output .= '<a href="'.esc_url( get_bloginfo( 'url' ).'/color/'.$parent->description.'/' ).'" class="cpg__dominant-color cpg__color-item" style="background-color:'.esc_attr($dominant).';" data-title="Dominant: '.esc_attr($dominant).'"></a>';
	    }

		if( $palette ){
			$output .= '<ul class="cpg__palette-list">';
			shuffle($palette);
			foreach ( $palette as $color ) {
				if( is_object( $color ) ){
					$color = $color->name;
				}

				$output .= '<li class="cpg__palette-item cpg__color-item" style="background-color:'.esc_attr($color).';" data-title="'.esc_attr($color).'"></li>';
			}
			$output .= '</ul>';
		}

	    // set the attachment image size to 'large'
		$attachment_content = sprintf( '<div class="cpg__palette-holder"><div class="cpg__palette-item">%s</div></div>', $output);
	}
    return $attachment_content;
}

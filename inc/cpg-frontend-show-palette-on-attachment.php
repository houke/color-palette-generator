<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
//output palette after image
add_filter( 'prepend_attachment', 'custom_prepend_attachment' );
function custom_prepend_attachment( $attachment_content ){

	$options = get_option('cpg_options');
	if( isset( $options['show_on_attachment'] ) && $options['show_on_attachment'] == 'true' ){
	    wp_enqueue_style( 'cpg-frontend-styles-css' );

		$output = wp_get_attachment_link( 0, 'large', false );
		if( $colors = get_the_terms( 0, 'cpg_dominant_color' ) ) {
			$dominant = $colors[0];
	    	$dominant = $dominant->name;
	    	$output .= '<div class="cpg__dominant-color cpg__color-item" style="background-color:'.$dominant.';" data-title="Dominant: '.$dominant.'"></div>';
	    }

		if( $palette = get_the_terms( 0, 'cpg_palette' ) ){
			$output .= '<ul class="cpg__palette-list">';
			shuffle($palette);
			foreach ( $palette as $color ) {
				if( is_object( $color ) ){
					$color = $color->name;
				}
				$output .= '<li class="cpg__palette-item cpg__color-item" style="background-color:'.$color.';" data-title="'.$color.'"></li>';
			}
			$output .= '</ul>';
		}

	    // set the attachment image size to 'large'
		$attachment_content = sprintf( '<div class="cpg__palette-holder"><div class="cpg__palette-item">%s</div></div>', $output);
	}
    return $attachment_content;
}

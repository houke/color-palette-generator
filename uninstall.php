<?php
	if (!defined('WP_UNINSTALL_PLUGIN')) {
	    die;
	}

	// Delete all taxonomies
	foreach ( array( 'cpg_dominant_color', 'cpg_palette' ) as $taxonomy ) {
		$wpdb->get_results( $wpdb->prepare( "DELETE t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s')", $taxonomy ) );
		$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
	}

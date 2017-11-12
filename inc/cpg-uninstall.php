<?php
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	function cpg_uninstall(){
		global $wpdb;
		foreach ( array( 'cpg_dominant_color', 'cpg_palette' ) as $taxonomy ) {
			$wpdb->query( "
				DELETE FROM
				{$wpdb->terms}
				WHERE term_id IN
				( SELECT * FROM (
					SELECT {$wpdb->terms}.term_id
					FROM {$wpdb->terms}
					JOIN {$wpdb->term_taxonomy}
					ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
					WHERE taxonomy = '$taxonomy'
				) as T
				);
			" );

			$wpdb->query( "DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = '$taxonomy'" );
		}
		delete_option( 'cpg_dominant_color_children' );
		delete_option( 'cpg_options' );
	}

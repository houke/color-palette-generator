<?php
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit();
	}

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


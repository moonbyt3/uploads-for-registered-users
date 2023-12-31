<?php

/**
 * Fired when the plugin is uninstalled.
 *
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'ufru_max_number_of_uploads');
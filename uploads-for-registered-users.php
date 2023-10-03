<?php
/**
 * Plugin Name: Uploads For Registered Users
 * Description: Adds option for Subscribers to upload their media inside WordPress dashboard. Admins get to preview all users uploads.
 * Version: 1.0
 * Author: Milorad Jekic
 * Author URI:        https://github.com/moonbyt3
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/moonbyt3/uploads-for-registered-users/
 * Text Domain:       uploads-for-registered-users
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME_VERSION', '1.0.0' );
define( 'UFRU_PLUGIN_PATH', __FILE__);

// Check if user registration is enabled in WordPress
if ( get_option( 'users_can_register' ) ) {
	// Add hooks
	add_filter( 'wp_handle_upload_prefilter', 'restrict_uploads' );
	add_filter( 'wp_handle_upload', 'restore_original_upload_prefilter' );

	/**
	 * Adds option for Subscribers to upload their media inside WordPress dashboard. Admins get to preview all users uploads.
	 *
	 * @param array $file An array of upload data.
	 * @return array Modified array of upload data.
	 */
	function restrict_uploads( $file ) {
		if ( ! is_user_logged_in() ) {
			$file['error'] = __( 'Only registered users with role Subscriber can upload media.', 'uploads-for-registered-users' );
		}
		return $file;
	}

	/**
	 * Restore the original upload prefilter if user is not logged in.
	 *
	 * @param array $file An array of upload data.
	 * @return array Original array of upload data.
	 */
	function restore_original_upload_prefilter( $file ) {
		if ( ! is_user_logged_in() ) {
			remove_filter( 'wp_handle_upload', 'restore_original_upload_prefilter' );
		}
		return $file;
	}

	// Enqueue scripts and styles for dashboard pages
	function custom_dashboard_scripts() {
		wp_enqueue_style( 'ufru-main-styles-admin', plugin_dir_url( __FILE__ ) . 'admin/css/main.css', [], '1.0', 'all' );
		wp_enqueue_style( 'ufru-main-styles-users', plugin_dir_url( __FILE__ ) . 'public/css/main.css', [], '1.0', 'all' );
		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'ufru-main-js', plugin_dir_url( __FILE__ ) . 'public/js/main.js', [ 'jquery' ], '1.0', true );
	}

	add_action( 'admin_enqueue_scripts', 'custom_dashboard_scripts' );



	// Add plugin files
	require_once( plugin_dir_path( __FILE__ ) . '/activate.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/deactivate.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/inc/utils.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/public/uploads.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/admin/settings.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/admin/user-files.php' );
} else {
	// User registration is not enabled
	function ufru_error_registration_disabled() {
		?>
		<div class="error notice">
			<p>
				<?php _e( 'Error in plugin: <b>Uploads For Registrated Users</b>', 'uploads-for-registered-users' ); ?>
			</p>
			<p>
				<?php _e( '"Anyone can register" option is disabled in <a href="/wp-admin/options-general.php">Settings->General->Membership</a>. Please enable this option.', 'uploads-for-registered-users' ); ?>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'ufru_error_registration_disabled' );
}
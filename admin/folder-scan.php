<?php
class UFRUFolderScan {
	function __construct() {
		add_action('admin_menu', [$this, 'add_folder_scan_page'], 9998);
	}

    /**
     * Folder scan page callback
     */
    public function add_folder_scan_page() {
        add_submenu_page(
			'uploads-for-registered-users',
			'Folder Scan',
			'Folder Scan',
			'manage_options',
			'ufru-settings-admin-folder-scan',
			[ $this, 'create_admin_page_folder_scan' ]
		);
    }

	/**
	 * Folder scan page callback
	 */
	public function create_admin_page_folder_scan() {
		// Get the list of registered users
		$registered_users = get_users();


		$all_users_potential_folders = []; // Initialize an empty array

		foreach ( $registered_users as $user ) {
			// Get user ID and user login (username)
			$user_id = $user->ID;
			$user_name = str_replace(' ', '_', $user->user_login);

			// Create the element in the format "userId_userName"
			$element = $user_id . '_' . $user_name;

			// Add the element to the array
			$all_users_potential_folders[] = $element;
		}

		// Get the path to the ufru directory
		$ufru_directory = ABSPATH . 'wp-content/uploads/ufru/';

		// Get the list of folders in the ufru directory
		$folders_in_uploads_dir = scandir( $ufru_directory );

		// Remove "." and ".." from the list
		$folders_in_uploads_dir = array_diff( $folders_in_uploads_dir, array( '..', '.' ) );

		$folders_to_delete = array_diff( $folders_in_uploads_dir, $all_users_potential_folders );

		if ( isset( $_POST['delete_folders'] ) ) {
			if ( isset( $_POST['folders_to_delete'] ) && is_array( $_POST['folders_to_delete'] ) ) {
				foreach ( $_POST['folders_to_delete'] as $folder_path ) {
					// Ensure that the folder path is within the ufru directory to prevent unauthorized deletions
					if ( strpos( $folder_path, $ufru_directory ) === 0 ) {
						// Perform folder deletion (you may want to add error handling)
						if ( is_dir( $folder_path ) ) {
							// Recursive removal
							system( "rm -rf " . escapeshellarg( $folder_path ) );

							echo '<div class="notice notice-success">' . esc_html__( 'Folder in path:', 'uploads-for-registered-users' ) . ' ' . esc_html( $folder_path ) . ' ' . esc_html__( 'has been deleted.', 'uploads-for-registered-users' ) . '</div>';
						} else {
							$errorMsg = '<div class="error notice">' . __('Error: Folder can not be created.', 'uploads-for-registered-users') .  '</div>';
							wp_die($errorMsg);
						}
					}
				}
			}
		}

		// Resync folder scan to prevent already deleted folder appearing inside the list
		$folders_in_uploads_dir = scandir( $ufru_directory );
		$folders_in_uploads_dir = array_diff( $folders_in_uploads_dir, array( '..', '.' ) );
		$folders_to_delete = array_diff( $folders_in_uploads_dir, $all_users_potential_folders );

		?>
		<div class="wrap">
			<h1>UFRU
				<?php _e( 'Folder Scan', 'uploads-for-registered-users' ); ?>
			</h1>
			<?php if ( $folders_to_delete ) : ?>
				<form method="post" class="ufru-form-scan-folder">
					<div class="notice is-dismissible">
						<p>
							<?php echo __( 'List of folders that don\'t appear to have registered users (user was removed)', 'uploads-for-registered-users' ); ?>
						</p>
					</div>
					<?php foreach ( $folders_to_delete as $user_folder ) :
						list( $user_id, $user_name ) = explode( '_', $user_folder );
						$folder_path = $ufru_directory . $user_folder;
						?>
						<label class="ufru-form-scan-folder__item">
							<input type="checkbox" name="folders_to_delete[]" value="<?php echo esc_attr( $folder_path ); ?>">
							<?php echo esc_html( $user_name ) . ' (' . esc_html( $folder_path ) . ')'; ?>
						</label>
					<?php endforeach; ?>

					<input type="submit" class="ufru-form-scan-folder__button button button-primary" name="delete_folders"
						value="Delete Selected Folders">
				</form>
			<?php else : ?>
				<div class="notice notice-info">
					<p>
						<?php echo __( 'All folders are synced, no difference between users list and user uploaded folders.', 'uploads-for-registered-users' ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

$folder_scan_page = new UFRUFolderScan();
?>
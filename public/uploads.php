<?php
class UFRU_Uploads {
	public function __construct() {
        add_action('admin_menu', [$this, 'add_page_uploads']);
    }

    /**
     * Add Upload Files page
     */
    public function add_page_uploads() {
		add_menu_page(
			'Uploads For Registered Users',
			'Uploads',
			'read',
			'uploads-for-registered-users',
			[$this, 'uploads_for_registered_users'],
			'dashicons-upload',
		);
    }
	/**
	 * Upload Files page callback
	 */
	public function uploads_for_registered_users() {
		$all_options = get_option( 'ufru_settings' );
		if (!empty($all_options) && isset($all_options['ufru_allowed_roles_to_upload_files'])) {
			$allowed_roles = $all_options['ufru_allowed_roles_to_upload_files'];
		} else {
			$all_options['ufru_allowed_roles_to_upload_files'] = [
				'subscriber'
			];
	
			update_option('ufru_settings', $all_options);
		}
	
		// Get the current user's roles
		$current_user = wp_get_current_user();
		$current_user_roles = $current_user->roles;
	
		if (array_intersect($current_user_roles, $allowed_roles)) {
			$plugin_name = 'ufru';
			$user_id = $current_user->ID;
			$user_name = preg_replace('/\s+/', '_', $current_user->user_login);
			$user_folder = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name; // Get the user's folder path
			$user_folder_url = wp_upload_dir()['baseurl'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name; // Get the user's folder URL
	
			// Handle file uploads
			if ( isset( $_POST['submit'] ) ) {
				$user_id = get_current_user_id();
				$current_number_of_files = count(glob($user_folder . '/*')); // Count existing files in the folder
				$max_files = urfu_calculate_max_number_of_uploads();
				$allowed_file_formats = get_option('ufru_settings')['ufru_allowed_file_types'];
	
				if ( ! file_exists( $user_folder ) ) {
					wp_mkdir_p( $user_folder );
				}
	
				if ( ! empty( $_FILES['files']['name'] ) ) {
					foreach ( $_FILES['files']['name'] as $key => $name ) {
						$file_extension = pathinfo($_FILES['files']['name'][$key], PATHINFO_EXTENSION);
						$file_extension = strtolower($file_extension);
	
						if (in_array($file_extension, explode(' ', $allowed_file_formats))) {
							$file_tmp = $_FILES['files']['tmp_name'][$key];
							$file_name = sanitize_file_name($name);
							$target_path = $user_folder . '/' . $file_name;
	
							// Check if user passed max number of upload files
							if ($current_number_of_files < $max_files) {
								// Check if the file already exists in the folder
								if (!file_exists($target_path)) {
									// Check if file is image
									if (is_array(@getimagesize($target_path))) {
										$file_info = wp_check_filetype($target_path);
										if (strpos($file_info['type'], 'image/') === 0) {
											move_uploaded_file($file_tmp, $target_path);
											$current_number_of_files++;
										}
									}
									move_uploaded_file($file_tmp, $target_path);
									$current_number_of_files++; // Increment the count of files in the folder
								}
							} else {
								$errorMsg = '<div class="error notice">' . __('Error: You have reached the maximum number of allowed uploads.', 'uploads-for-registered-users') .  '</div>';
								wp_die($errorMsg);
								break;
							}
						} else {
							$errorMsg =  '<div class="error notice">' . __("Error: File format not supported: .$file_extension", 'uploads-for-registered-users') .  '</div>';
							wp_die($errorMsg);
							break;
						}
					}
				}
			}
	
			// Handle file removal
			if ( isset( $_POST['remove_file'] ) ) {
				$file_name = $_POST['remove_file'];

				ufru_remove_file($user_id, $user_name, $file_name);
			}
	
			// Check if user directory exists
			if (is_dir($user_folder)) {
				$files = scandir( $user_folder );
				$files = array_diff( $files, array( '.', '..' ) ); // Remove "." and ".." entries
			}
	
			?>
	
			<div class="wrap">
				<h2>
					<?php _e( 'Upload Your Files', 'uploads-for-registered-users' ); ?>
				</h2>
				<p>
					<?php _e( 'Note: You can select multiple files to upload.', 'uploads-for-registered-users' ); ?>
				</p>
				<p>
					<?php _e( 'Max number of uploads:', 'uploads-for-registered-users' ); ?> <?php echo urfu_calculate_max_number_of_uploads(); ?>
				</p>
				<form 
					class="ufru-upload-form"
					method="post"
					enctype="multipart/form-data"
					js-upload-form
				>
					<input type="file" class="bfi" id="file_upload" name="files[]" multiple>
					<div class="ufru-upload-form__submit-btn">
						<input type="submit" class="button button-primary" name="submit" value="Upload File(s)" js-upload-files-form-submit>
					</div>
				</form>
				<?php if ( ! empty( $files ) ) : ?>
					<div class="ufru-upload-files">
						<h3>
							<?php _e( 'Uploaded Files', 'uploads-for-registered-users' ); ?>
							<span>(<?php echo count($files); ?> /  <?php echo urfu_calculate_max_number_of_uploads(); ?>)</span>
						</h3>
						<div class="ufru-upload-files__items">
							<?php foreach ( $files as $file ) : ?>
								<div class="ufru-upload-files__items-file ufru-file-preview">
									<?php
										$fileUrl = $user_folder_url . '/' . $file;
									?>
									<img 
										src="<?php echo $fileUrl; ?>"
										onerror="this.onerror=null;this.src='https\:\/\/placehold.co/200x200?text=File:\\n<?php echo $file . '\''; ?>"
										data-url="<?php echo $fileUrl; ?>"
										class="ufru-file-preview__img"
										alt="File Preview"
										width="200"
										height="200"
										loading="lazy"
										title="<?php echo $file; ?>"
									>
									<form method="post">
										<input type="hidden" name="remove_file" value="<?php echo $file; ?>">
										<button 
											class="
												ufru-file-preview__button
												ufru-file-preview__button--top-right
												ufru-file-preview__button-icon-remove
												ufru-button
												dashicons-before
												dashicons-no
											"
											title="<?php _e( 'Delete file', 'uploads-for-registered-users' ); ?>"
											type="submit"
										>
										</button>
										<span 
											class="
												ufru-file-preview__button 
												ufru-file-preview__button--bottom-right
												ufru-file-preview__button-icon-expand
												ufru-button
												dashicons-before
												dashicons-editor-expand
											"
											title="<?php _e( 'Open file in new tab', 'uploads-for-registered-users' ); ?>"
											js-ufru-open-file
										>
										</span>
									</form>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
	
			<?php
		} else {
			$errorMsg =  '<div class="	notice is-dismissible"><p>' . __("Error: Your account role doesn't have permission to preview this page", 'uploads-for-registered-users') .  '</p></div>';
			wp_die($errorMsg);
		}
	}
}

$ufru_user_uploads_page = new UFRU_Uploads();
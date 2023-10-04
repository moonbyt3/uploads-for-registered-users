<?php
// Add dashboard menu page to contain all plugin pages
function uploads_for_registered_users_menu_page() {
	add_menu_page(
		'Uploads For Registered Users',
		'Uploads',
		'read',
		'uploads-for-registered-users',
		'uploads_for_registered_users',
		'dashicons-upload',
	);
}
add_action( 'admin_menu', 'uploads_for_registered_users_menu_page' );

function uploads_for_registered_users() {
	$plugin_name = 'ufru';
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$user_name = preg_replace('/\s+/', '_', $current_user->display_name);
	$user_folder = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name; // Get the user's folder path
	$user_folder_url = wp_upload_dir()['baseurl'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name; // Get the user's folder URL

	// Handle file uploads
	if ( isset( $_POST['submit'] ) ) {
		$user_id = get_current_user_id();
		$upload_dir = wp_upload_dir();
		$user_folder = $upload_dir['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name;
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
		$filename = sanitize_file_name( $_POST['remove_file'] );
		$file_path = $user_folder . '/' . $filename;

		if ( file_exists( $file_path ) ) {
			unlink( $file_path ); // Delete the file
		}
	}

	$files = scandir( $user_folder );
	$files = array_diff( $files, array( '.', '..' ) ); // Remove "." and ".." entries
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
		<form method="post" enctype="multipart/form-data" js-upload-form>
			<input type="hidden" id="valid_extensions" value=".jpg .jpeg .png">
			<input type="file" id="file_upload" name="files[]" multiple>
			<input type="submit" name="submit" value="Upload" js-upload-files-form-submit>
		</form>
		<?php if ( ! empty( $files ) ) : ?>
			<div class="ufru-upload-filess">
				<h3>
					<?php _e( 'Uploaded Files', 'uploads-for-registered-users' ); ?>
				</h3>
				<div class="ufru-upload-files__wrapper">
					<?php foreach ( $files as $file ) : ?>
						<div class="ufru-file-preview">
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
								loading="lazy"
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
}

function isImageOrSvg($filePath) {
    // Get the image information
    $imageInfo = @getimagesize($filePath);

    if ($imageInfo === false) {
        // The file is not a valid image
        return false;
    }

    // Check the MIME type
    $mime = $imageInfo['mime'];

    // List of valid image and SVG MIME types
    $validMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/svg+xml',
    ];

    // Check if the MIME type is in the list of valid types
    return in_array($mime, $validMimeTypes);
}

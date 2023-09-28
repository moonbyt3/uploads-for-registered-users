<?php
// Add dashboard menu page to contain all plugin pages
function uploads_for_registered_users_menu_page() {
	add_menu_page(
		'Uploads For Registered Users',
		'Uploads',
		'subscriber',
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
	
	// Handle image uploads
	if ( isset( $_POST['submit'] ) ) {
		$user_id = get_current_user_id();
		$upload_dir = wp_upload_dir();
		$user_folder = $upload_dir['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name;

		if ( ! file_exists( $user_folder ) ) {
			wp_mkdir_p( $user_folder );
		}

		if ( ! empty( $_FILES['images']['name'] ) ) {
			foreach ( $_FILES['images']['name'] as $key => $name ) {
				$image_tmp = $_FILES['images']['tmp_name'][ $key ];
				$image_name = sanitize_file_name( $name );
				move_uploaded_file( $image_tmp, $user_folder . '/' . $image_name );
			}
		}
	}

	// Handle image removal
	if ( isset( $_POST['remove_image'] ) ) {
		$image_filename = sanitize_file_name( $_POST['remove_image'] );
		$image_path = $user_folder . '/' . $image_filename;

		if ( file_exists( $image_path ) ) {
			unlink( $image_path ); // Delete the image file
		}
	}

	$images = scandir( $user_folder );
	$images = array_diff( $images, array( '.', '..' ) ); // Remove "." and ".." entries
	?>

	<div class="wrap">
		<h2>
			<?php _e( 'Upload Your Images', 'uploads-for-registered-users' ); ?>
		</h2>
		<p>
			<?php _e( 'Note: You can select multiple images to upload.', 'uploads-for-registered-users' ); ?>
		</p>
		<form method="post" enctype="multipart/form-data">
			<input type="file" id="file_upload" accept="image/jpeg, image/png, image/jpg" name="images[]" multiple>
			<input type="submit" name="submit" value="Upload" js-upload-images-form-submit>
		</form>
		<?php if ( ! empty( $images ) ) : ?>
			<div class="ufru-upload-images">
				<h3>
					<?php _e( 'Uploaded Images', 'uploads-for-registered-users' ); ?>
				</h3>
				<div class="ufru-upload-images__wrapper">
					<?php foreach ( $images as $image ) : ?>
						<div class="ufru-image-preview">
							<img 
								src="<?php echo $user_folder_url . '/' . $image; ?>"
								class="ufru-image-preview__img"
								alt="Image Preview"
								width="200"
								loading="lazy"
							>
							<form method="post">
								<input type="hidden" name="remove_image" value="<?php echo $image; ?>">
								<button 
									class="
										ufru-image-preview__button
										ufru-image-preview__button--top-right
										ufru-image-preview__button-icon-remove
										ufru-button
										dashicons-before
										dashicons-no
									"
									title="<?php _e( 'Delete image', 'uploads-for-registered-users' ); ?>"
									type="submit"
								>
								</button>
								<span 
									class="
										ufru-image-preview__button 
										ufru-image-preview__button--bottom-right
										ufru-image-preview__button-icon-expand
										ufru-button
										dashicons-before
										dashicons-editor-expand
									"
									title="<?php _e( 'Open full screen image', 'uploads-for-registered-users' ); ?>"
									js-ufru-open-image
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
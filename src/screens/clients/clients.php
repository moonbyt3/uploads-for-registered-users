<?php
// Add a submenu page for subscribers
function custom_dashboard_submenu() {
	add_menu_page(
		'Upload Images',
		'Upload Images',
		'read',
		'upload-images',
		'custom_subscriber_dashboard',
		'dashicons-upload'
	);
}
add_action( 'admin_menu', 'custom_dashboard_submenu' );

function custom_subscriber_dashboard() {
	$user_id = get_current_user_id();
	$user_folder = wp_upload_dir()['basedir'] . '/' . $user_id; // Get the user's folder path
	$user_folder_url = wp_upload_dir()['baseurl'] . '/' . $user_id; // Get the user's folder URL

	// Handle image uploads
	if ( isset( $_POST['submit'] ) ) {
		$user_id = get_current_user_id();
		$upload_dir = wp_upload_dir();
		$user_folder = $upload_dir['basedir'] . '/' . $user_id;

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
			<?php _e( 'Upload Images', 'uploads-for-registered-users' ); ?>
		</h2>
		<p>
			<?php _e( 'Note: You can select multiple images to upload.', 'uploads-for-registered-users' ); ?>
		</p>
		<form method="post" enctype="multipart/form-data">
			<input type="file" id="file_upload" accept="image/jpeg, image/png, image/jpg" name="images[]" multiple>
			<input type="submit" name="submit" value="Upload">
		</form>
		<?php if ( ! empty( $images ) ) : ?>
			<div class="ufru-upload-images">
				<h3>
					<?php _e( 'Uploaded Images', 'uploads-for-registered-users' ); ?>
				</h3>
				<div class="ufru-upload-images__wrapper">
					<?php foreach ( $images as $image ) : ?>
						<div class="ufru-image-preview">
							<img src="<?php echo $user_folder_url . '/' . $image; ?>" class="ufru-image-preview__img"
								alt="Image Preview" width="200">
							<form method="post">
								<input type="hidden" name="remove_image" value="<?php echo $image; ?>">
								<button class="ufru-image-preview__button" type="submit" title="Delete image">
									<div class="ufru-image-preview__button-icon-remove">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 1024 1024">
											<path fill="#FAFCFB" d="M724.3 198H296.1l54.1-146.6h320z" />
											<path fill="#0F0F0F"
												d="M724.3 216.5H296.1c-6.1 0-11.7-3-15.2-7.9-3.5-5-4.3-11.3-2.2-17L332.8 45c2.7-7.3 9.6-12.1 17.4-12.1h320c7.7 0 14.7 4.8 17.4 12.1l54.1 146.6c2.1 5.7 1.3 12-2.2 17-3.5 4.9-9.2 7.9-15.2 7.9zm-401.6-37h375.1L657.3 69.9H363.1l-40.4 109.6z" />
											<path fill="#9DC6AF"
												d="M664.3 981.6H339.7c-54.2 0-98.5-43.3-99.6-97.5L223.7 235h572.9l-32.8 651.4c-2.3 53.2-46.1 95.2-99.5 95.2z" />
											<path fill="#191919"
												d="M664.3 995H339.7c-29.7 0-57.8-11.4-79-32.2-21.2-20.8-33.3-48.6-34-78.3L210 221.6h600.7L777.2 887c-2.6 60.5-52.2 108-112.9 108zM237.4 248.3l16 635.5c.5 22.7 9.7 44 25.9 59.8 16.2 15.9 37.7 24.6 60.4 24.6h324.6c46.3 0 84.2-36.2 86.2-82.5l32.1-637.4H237.4z" />
											<path fill="#D39E33"
												d="M827.1 239.5H193.3c-22.2 0-40.4-18.2-40.4-40.4v-2.2c0-22.2 18.2-40.4 40.4-40.4h633.8c22.2 0 40.4 18.2 40.4 40.4v2.2c0 22.2-18.2 40.4-40.4 40.4z" />
											<path fill="#111"
												d="M826 252.9H194.4c-30.3 0-54.9-24.6-54.9-54.9 0-30.3 24.6-54.9 54.9-54.9H826c30.3 0 54.9 24.6 54.9 54.9s-24.7 54.9-54.9 54.9zm-631.6-83.1c-15.5 0-28.2 12.6-28.2 28.2s12.6 28.2 28.2 28.2H826c15.5 0 28.2-12.6 28.2-28.2 0-15.5-12.6-28.2-28.2-28.2H194.4z" />
											<path fill="#FAFCFB" d="M354.6 430.3v369.6" />
											<path fill="#0F0F0F"
												d="M354.6 813.3c-7.4 0-13.4-6-13.4-13.4V430.3c0-7.4 6-13.4 13.4-13.4s13.4 6 13.4 13.4v369.6c-.1 7.4-6 13.4-13.4 13.4z" />
											<path fill="#FAFCFB" d="M458.3 430.3v369.6" />
											<path fill="#0F0F0F"
												d="M458.3 813.3c-7.4 0-13.4-6-13.4-13.4V430.3c0-7.4 6-13.4 13.4-13.4s13.4 6 13.4 13.4v369.6c0 7.4-6 13.4-13.4 13.4z" />
											<path fill="#FAFCFB" d="M562.1 430.3v369.6" />
											<path fill="#0F0F0F"
												d="M562.1 813.3c-7.4 0-13.4-6-13.4-13.4V430.3c0-7.4 6-13.4 13.4-13.4s13.4 6 13.4 13.4v369.6c-.1 7.4-6.1 13.4-13.4 13.4z" />
											<path fill="#FAFCFB" d="M665.8 430.3v369.6" />
											<path fill="#0F0F0F"
												d="M665.8 813.3c-7.4 0-13.4-6-13.4-13.4V430.3c0-7.4 6-13.4 13.4-13.4s13.4 6 13.4 13.4v369.6c0 7.4-6 13.4-13.4 13.4z" />
										</svg>
									</div>
								</button>
								<span class="ufru-image-preview__button-open" js-ufru-open-image>
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
										<path stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M4 8.5V4m0 0h4.5M4 4l5.5 5.5m10.5-1V4m0 0h-4.5M20 4l-5.5 5.5M4 15.5V20m0 0h4.5M4 20l5.5-5.5m10.5 1V20m0 0h-4.5m4.5 0-5.5-5.5" />
									</svg>
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
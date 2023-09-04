<?php

// Add an admin menu page to list users and their images
function custom_admin_page() {
	add_menu_page(
		'User Images',
		'User Images',
		'manage_options',
		'user-images',
		'custom_user_images_page'
	);
}
add_action( 'admin_menu', 'custom_admin_page' );

// Admin page content
function custom_user_images_page() {
	// Handle image removal
	if ( isset( $_POST['remove_image'] ) && isset( $_POST['user_id'] ) ) {
		$user_id = $_POST['user_id'];
		$upload_dir = wp_upload_dir();
		$user_folder = $upload_dir['basedir'] . '/' . $user_id;
		$image_filename = sanitize_file_name( $_POST['remove_image'] );
		$image_path = $user_folder . '/' . $image_filename;

		if ( file_exists( $image_path ) ) {
			unlink( $image_path ); // Delete the image file
		}
	}
	?>

	<div class="wrap">
		<h2>
			<?php _e( 'User Images', 'uploads-for-registered-users' ); ?>
		</h2>
		<table class="ufru-table wp-list-table widefat striped">
			<thead>
				<tr class="ufru-table__row">
					<th>
						<?php _e( 'User ID', 'uploads-for-registered-users' ); ?>
					</th>
					<th>
						<?php _e( 'Username', 'uploads-for-registered-users' ); ?>
					</th>
					<th>
						<?php _e( 'Uploaded Images', 'uploads-for-registered-users' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$blogusers = get_users();
				foreach ( $blogusers as $user ) {
					$user_id = $user->ID;
					$user_folder = wp_upload_dir()['basedir'] . '/' . $user_id;
					$images = scandir( $user_folder );
					$image_url = wp_upload_dir()['baseurl'] . '/' . $user_id;
					?>
					<?php if ( ! empty( $images ) ) : ?>
						<tr class="ufru-table__row">
							<td class="ufru-table__row-cell">
								<?php echo $user_id; ?>
							</td>
							<td class="ufru-table__row-cell">
								<?php echo $user->user_login; ?>
							</td>
							<td class="ufru-table__row-cell">
								<div class="ufru-upload-images__wrapper">
									<?php foreach ( $images as $image ) : ?>
										<?php if ( $image != '.' && $image != '..' ) : ?>
											<div class="ufru-image-preview">
												<img src="<?php echo $image_url . '/' . $image; ?>" class="ufru-image-preview__img"
													alt="User Image" width="200">
												<form method="post">
													<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
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
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</td>
						</tr>
					<?php endif; ?>
				<?php } ?>
			</tbody>
		</table>
	</div>

<?php } ?>
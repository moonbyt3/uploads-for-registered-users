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
add_action('admin_menu', 'custom_admin_page');

// Admin page content
function custom_user_images_page() {
    ?>

    <div class="wrap">
        <h2><?php __( 'User Images', 'uploads-for-registered-users' ); ?></h2>
        <table class="ufru-table wp-list-table widefat striped">
            <thead>
                <tr class="ufru-table__row">
                    <th><?php __( 'User ID', 'uploads-for-registered-users' ); ?></th>
                    <th><?php __( 'Username', 'uploads-for-registered-users' ); ?></th>
                    <th><?php __( 'Uploaded Images', 'uploads-for-registered-users' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $blogusers = get_users();
                    foreach ($blogusers as $user) {
                        $user_id = $user->ID;
                        $user_folder = wp_upload_dir()['basedir'] . '/' . $user_id;
                        $images = scandir($user_folder);
                        $image_url = wp_upload_dir()['baseurl'] . '/' . $user_id;
                ?>

                    <tr class="ufru-table__row">
                        <td class="ufru-table__row-cell"><?php echo $user_id; ?></td>
                        <td class="ufru-table__row-cell"><?php echo $user->user_login; ?></td>
                        <td class="ufru-table__row-cell">
                            <div class="ufru-images-grid">
                                <?php foreach ($images as $image): ?>
                                    <?php if ($image != '.' && $image != '..'): ?>
                                        <div class="ufru-images-grid__media">
                                            <img src="<?php echo $image_url . '/' . $image; ?>" alt="User Image">
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
    </div>

<?php } ?>
<?php
// Add an admin menu page to list users and their images
function user_images_admin_page() {
	add_submenu_page(
		'uploads-for-registered-users',
		'User Images',
		'User Images',
		'manage_options',
		'user_images',
		'user_images_screen',
		1
	);
}
add_action( 'admin_menu', 'user_images_admin_page' );

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
// Admin page content
class User_Images_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct(array(
            'singular' => 'user_image',
            'plural' => 'user_images',
            'ajax' => false
        ));
    }

    public function column_default($item, $column_name) {
        return $item[$column_name];
    }


    public function column_uploaded_images($item) {
        $user_id = $item['user_id'];
        $user_folder = wp_upload_dir()['basedir'] . '/' . $user_id;
        $images = scandir($user_folder);
        $image_url = wp_upload_dir()['baseurl'] . '/' . $user_id;

        if (!empty($images)) {
			ob_start(); ?>
			<div class="ufru-upload-images__wrapper">
			<?php ob_end_flush();
				foreach ($images as $image) { 
				?>
					<div class="ufru-upload-images__wrapper">
						<div class="ufru-image-preview">
							<img src="<?php echo $image_url . '/' . $image ?>" class="ufru-image-preview__img" alt="User Image" width="200" loading="lazy">
							<form method="post">
								<input type="hidden" name="user_id" value="<?php $user_id; ?>" />
								<input type="hidden" name="remove_image" value="<?php $image; ?>">
								<button class="ufru-image-preview__button ufru-image-preview__button--top-right ufru-image-preview__button-icon-remove ufru-button dashicons-before dashicons-no" title="<?php echo __('Delete image', 'uploads-for-registered-users'); ?>" type="submit"></button>
								<span class="ufru-image-preview__button ufru-image-preview__button--bottom-right ufru-image-preview__button-icon-expand ufru-button dashicons-before dashicons-editor-expand" title="<?php echo __('Open full screen image', 'uploads-for-registered-users'); ?>" js-ufru-open-image></span>
							</form>
						</div>
					</div>
				<?php }
			ob_end_flush();
        } else {
            return '';
        }
    }

    public function get_columns() {
        $columns = array(
            'user_id' => __('User ID', 'uploads-for-registered-users'),
            'user_login' => __('Username', 'uploads-for-registered-users'),
            'uploaded_images' => __('Uploaded Images', 'uploads-for-registered-users'),
        );
        return $columns;
    }

    public function prepare_items() {
        $this->_column_headers = [
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		];
        $data = $this->get_user_images_data();
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));

        $this->items = array_slice($data, ($current_page - 1) * $per_page, $per_page);
    }

    private function get_user_images_data() {
        $data = array();
        $blogusers = get_users();

        foreach ($blogusers as $user) {
            $user_id = $user->ID;
            $user_folder = wp_upload_dir()['basedir'] . '/' . $user_id;

            if (is_dir($user_folder)) {
                $images = scandir($user_folder);
                if (!empty($images)) {
                    $data[] = array(
                        'user_id' => $user_id,
                        'user_login' => $user->user_login,
                    );
                }
            }
        }

        return $data;
    }
}

function user_images_screen() {
    $user_images_table = new User_Images_List_Table();
    $user_images_table->prepare_items();
    ?>

    <div class="wrap">
        <h2><?php _e('User Images', 'uploads-for-registered-users'); ?></h2>
        <form method="post">
            <input type="hidden" name="page" value="user-images-screen">
			<div class="ufru-table-user-images">
				<?php $user_images_table->display(); ?>
			</div>
        </form>
    </div>

    <?php
}
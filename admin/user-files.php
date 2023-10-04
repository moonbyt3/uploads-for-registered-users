<?php
// Add an admin menu page to list users and their files
function user_files_admin_page() {
	add_submenu_page(
		'uploads-for-registered-users',
		'User Files',
		'User Files',
		'read',
		'user_files',
		'user_files_screen',
	);
}
add_action( 'admin_menu', 'user_files_admin_page' );

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( isset( $_POST['user_id'] ) ) {
	$plugin_name = 'ufru';
	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];

	$user_folder = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name; // Get the user's folder path
	$filename = sanitize_file_name( $_POST['remove_file'] );
	
	$file_path = $user_folder . '/' . $filename;

	// Delete the file
	if ( file_exists( $file_path ) ) {
		unlink( $file_path );
	}
}

// Admin page content
class User_Files_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct(array(
            'singular' => 'user_file',
            'plural' => 'user_files',
            'ajax' => false
        ));
    }

    public function column_default($item, $column_name) {
        return $item[$column_name];
    }


    public function column_uploaded_files($item) {
		$plugin_name = 'ufru';
        $user_id = $item['user_id'];
		$user_name = preg_replace('/\s+/', '_', $item['user_login']);

		$user_uploads_folder = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name;

		$file_url = wp_upload_dir()['baseurl'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name;
        $file = array_diff(scandir($user_uploads_folder), array('..', '.'));
        $user_folder_url = wp_upload_dir()['baseurl'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name; // Get the user's folder URL
        
		if (!empty($file)) {
			ob_start(); ?>
			<div class="ufru-upload-files__wrapper">
			<?php ob_end_flush();
				ob_start();
				foreach ($file as $file) { 
				?>
					<div class="ufru-file-preview">
                        <?php
                            $fileUrl = $user_folder_url . '/' . $file;
                        ?>
						<img
                            src="<?php echo $file_url . '/' . $file ?>"
                            onerror="this.onerror=null;this.src='https\:\/\/placehold.co/200x200?text=File <?php echo $file . '\''; ?>"
                            data-url="<?php echo $fileUrl; ?>"
                            class="ufru-file-preview__img"
                            alt="User File"
                            width="200"
                            height="200"
                            loading="lazy"
                        >
						<form method="post">
							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
							<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
							<input type="hidden" name="user_name" value="<?php echo $user_name; ?>" />
							<input type="hidden" name="remove_file" value="<?php echo $file; ?>">
							<button 
								class="ufru-file-preview__button ufru-file-preview__button--top-right ufru-file-preview__button-icon-remove ufru-button dashicons-before dashicons-no"
								title="<?php echo __('Delete file', 'uploads-for-registered-users'); ?>"
								type="submit"
								id="submit"
							></button>
							<span class="ufru-file-preview__button ufru-file-preview__button--bottom-right ufru-file-preview__button-icon-expand ufru-button dashicons-before dashicons-editor-expand" title="<?php echo __('Open in full screen', 'uploads-for-registered-users'); ?>" js-ufru-open-file></span>
						</form>
					</div>
				<?php } ?>
			<?php
			ob_end_flush();
			ob_start(); ?>
			</div>
			<?php ob_end_flush();
        } else {
            return '';
        }
    }

    public function get_columns() {
        $columns = array(
            'user_id' => __('User ID', 'uploads-for-registered-users'),
            'user_login' => __('Username', 'uploads-for-registered-users'),
            'uploaded_files' => __('Uploaded Files', 'uploads-for-registered-users'),
        );
        return $columns;
    }

    public function prepare_items() {
        $this->_column_headers = [
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		];
        $data = $this->get_user_files_data();
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));

        $this->items = array_slice($data, ($current_page - 1) * $per_page, $per_page);
    }

    private function get_user_files_data() {
        $data = array();
        $blogusers = get_users();

        foreach ($blogusers as $user) {
			$plugin_name = 'ufru';
            $user_id = $user->ID;
			$user_name = preg_replace('/\s+/', '_', $user->display_name);
            $user_folder = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name;

            if (is_dir($user_folder)) {
                $file = scandir($user_folder);
                if (!empty($file)) {
                    $data[] = array(
                        'user_id' => $user_id,
                        'user_login' => $user->display_name,
                    );
                }
            }
        }

        return $data;
    }
}

function user_files_screen() {
    $user_files_table = new User_Files_List_Table();
    $user_files_table->prepare_items();
    ?>

    <div class="wrap">
        <h2><?php _e('User Files', 'uploads-for-registered-users'); ?></h2>

		<div class="ufru-table-user-files">
			<?php $user_files_table->display(); ?>
		</div>
    </div>

    <?php
}
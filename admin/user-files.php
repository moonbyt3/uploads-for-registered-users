<?php
// Add an admin menu page to list users and their files
function add_page_user_files() {
    add_submenu_page(
        'uploads-for-registered-users',
        'User Files',
        'User Files',
        'manage_options',
        'user_files',
        'user_files_screen',
    );
}
add_action( 'admin_menu', 'add_page_user_files' );

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
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

    public function column_user_login($item) {
        $user_name = $item['user_login'];
        $confirmationQuestion = sprintf( __( 'This will delete files for user: %s', 'uploads-for-registered-users' ), $user_name );
        $actions = [
            'delete'    => sprintf('<a href="?page=%s&action=delete&folder=%s" onclick="return confirm(\'' . $confirmationQuestion . '\')">%s</a>', $_REQUEST['page'], $item['user_id'] . '_' . $item['user_login'], __('Delete All Files', 'uploads-for-registered-users')),
        ];
        return sprintf('%1$s %2$s', $item['user_login'], $this->row_actions($actions) );
    }

    /**
     * Delete all files from specified folder
     *
     * @param string $folder Path of the folder to delete
     */
    public function delete_all_user_files($folder) {
        $plugin_name = 'ufru';
        $user_uploads_folder = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $folder;

        var_dump($_GET);

        
        // Delete the file
        if ( file_exists( $user_uploads_folder ) ) {
            // Delete the file
            require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
            require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
            $fileSystemDirect = new WP_Filesystem_Direct(false);
            $fileSystemDirect->rmdir($user_uploads_folder, true);
        }

        wp_redirect('/wp-admin/admin.php?page=user_files');
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
                            title="<?php echo $file; ?>"
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
							<span class="ufru-file-preview__button ufru-file-preview__button--bottom-right ufru-file-preview__button-icon-expand ufru-button dashicons-before dashicons-editor-expand" title="<?php echo __('Open file in new tab', 'uploads-for-registered-users'); ?>" js-ufru-open-file></span>
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
            'user_login' => __('User Name', 'uploads-for-registered-users'),
            'uploaded_files' => __('Uploaded Files', 'uploads-for-registered-users'),
        );
        return $columns;
    }

    public function prepare_items() {
        $this->_column_headers = [
			$this->get_columns(),
			[],
			$this->get_sortable_columns(),
		];
        $data = $this->get_user_files_data();
        $per_page = 8;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));

        $this->items = array_slice($data, ($current_page - 1) * $per_page, $per_page);
        $this->process_bulk_action();
    }

    private function get_user_files_data() {
        $data = array();
        $blogusers = get_users();

        foreach ($blogusers as $user) {
			$plugin_name = 'ufru';
            $user_id = $user->ID;
			$user_name = preg_replace('/\s+/', '_', $user->user_login);
            $user_folder = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name;

            if (is_dir($user_folder)) {
                $file = scandir($user_folder);
                if (!empty($file)) {
                    $data[] = array(
                        'user_id' => $user_id,
                        'user_login' => $user->user_login,
                    );
                }
            }
        }

        return $data;
    }

    public function process_bulk_action() {
        $action = $this->current_action();
        switch ( $action ) {
            case 'delete':
                $folder_to_delete = $_GET['folder'];
                $this->delete_all_user_files($folder_to_delete);
                break;
            default:
                return;
                break;
        }
        return;
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
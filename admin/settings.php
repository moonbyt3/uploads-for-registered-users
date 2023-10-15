<?php
class UFRUSettingsPage
{
    private $options;

    public function __construct() {
        add_action('admin_menu', [$this, 'add_plugin_settings_page'], 9999);
        add_action('admin_init', [$this, 'page_init']);
    }

    /**
     * Add options page
     */
    public function add_plugin_settings_page() {
        add_submenu_page(
            'uploads-for-registered-users',
            'Settings',
            'Settings',
            'manage_options',
            'ufru-settings-admin',
            [$this, 'create_admin_page']
        );

        add_submenu_page(
            'uploads-for-registered-users',
            'Folder Scan',
            'Folder Scan',
            'manage_options',
            'ufru-settings-admin-folder-scan',
            [$this, 'create_admin_page_folder_scan']
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'ufru_settings' );

        ?>
        <div class="wrap">
            <h1>UFRU <?php _e('Settings', 'uploads-for-registered-users'); ?></h1>

            <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'ufru_general_settings' );
                    do_settings_sections( 'ufru-settings-admin' );
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Folder scan page callback
     */
    public function create_admin_page_folder_scan() {
        // Get the list of registered users
        $registered_users = get_users();


        $all_users_potential_folders = []; // Initialize an empty array

        foreach ($registered_users as $user) {
            // Get user ID and user login (username)
            $user_id = $user->ID;
            $user_name = $user->user_login;

            // Create the element in the format "userId_userName"
            $element = $user_id . '_' . $user_name;

            // Add the element to the array
            $all_users_potential_folders[] = $element;
        }



        // Get the path to the ufru directory
        $ufru_directory = ABSPATH . 'wp-content/uploads/ufru/';

        // Get the list of folders in the ufru directory
        $folders_in_uploads_dir = scandir($ufru_directory);

        // Remove "." and ".." from the list
        $folders_in_uploads_dir = array_diff($folders_in_uploads_dir, array('..', '.'));

        $folders_to_delete = array_diff($folders_in_uploads_dir, $all_users_potential_folders);

        if (isset($_POST['delete_folders'])) {
            if (isset($_POST['folders_to_delete']) && is_array($_POST['folders_to_delete'])) {
                foreach ($_POST['folders_to_delete'] as $folder_path) {
                    // Ensure that the folder path is within the ufru directory to prevent unauthorized deletions
                    if (strpos($folder_path, $ufru_directory) === 0) {
                        // Perform folder deletion (you may want to add error handling)
                        if (is_dir($folder_path)) {
                            // Recursive removal
                            system("rm -rf " . escapeshellarg($folder_path));
                            
                            echo '<div class="notice notice-success">' . esc_html__('Folder in path:', 'uploads-for-registered-users') . ' ' . esc_html($folder_path) . ' ' . esc_html__('has been deleted.', 'uploads-for-registered-users') . '</div>';
                        }
                    }
                }
            }
        }

        // Resync folder scan to prevent already deleted folder appearing inside the list
        $folders_in_uploads_dir = scandir($ufru_directory);
        $folders_in_uploads_dir = array_diff($folders_in_uploads_dir, array('..', '.'));
        $folders_to_delete = array_diff($folders_in_uploads_dir, $all_users_potential_folders);

        ?>
            <div class="wrap">
                <h1>UFRU <?php _e('Folder Scan', 'uploads-for-registered-users'); ?></h1>
                <?php if ($folders_to_delete) : ?>
                    <form method="post" class="ufru-form-scan-folder">
                        <div class="notice is-dismissible">
                            <p><?php echo __('List of folders that don\'t appear to have registered users (user was removed)', 'uploads-for-registered-users'); ?></p>
                        </div>
                        <?php foreach ($folders_to_delete as $user_folder) : 
                            list($user_id, $user_name) = explode('_', $user_folder);
                            $folder_path = $ufru_directory . $user_folder;
                        ?>
                            <label class="ufru-form-scan-folder__item">
                                <input type="checkbox" name="folders_to_delete[]" value="<?php echo esc_attr($folder_path); ?>">
                                <?php echo esc_html($user_name) . ' (' . esc_html($folder_path) . ')'; ?>
                            </label>
                        <?php endforeach; ?>

                        <input type="submit" class="ufru-form-scan-folder__button button button-primary" name="delete_folders" value="Delete Selected Folders">
                    </form>
                <?php else: ?>
                    <div class="notice notice-info">
                        <p><?php echo __('All folders are synced, no difference between users list and user uploaded folders.', 'uploads-for-registered-users'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {        
        register_setting(
            'ufru_general_settings', // Option group
            'ufru_settings', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        add_settings_section(
            'settings_section_general', // ID
            'General settings', // Title
            null, // Callback
            'ufru-settings-admin' // Page
        );  

        add_settings_field(
            'ufru_max_number_of_uploads', // ID
            'Max number of uploads', // Title 
            [$this, 'input_max_number_of_uploads_callback'], // Callback
            'ufru-settings-admin', // Page
            'settings_section_general', // Section
            [
                'name' => 'ufru_max_number_of_uploads',
                'label_for' => 'ufru_max_number_of_uploads',
            ]
        );

        add_settings_field(
            'ufru_allowed_file_types', // ID
            'Allowed formats', // Title
            [$this, 'input_allowed_formats_callback'], // Callback
            'ufru-settings-admin', // Page
            'settings_section_general', // Section
            [
                'name' => 'ufru_allowed_file_types',
                'label_for' => 'ufru_allowed_file_types',
            ]
        );

        add_settings_field(
            'ufru_max_file_size', // ID
            'Maximum file size', // Title
            [$this, 'input_max_file_size_callback'], // Callback
            'ufru-settings-admin', // Page
            'settings_section_general', // Section
            [
                'name' => 'ufru_max_file_size',
                'label_for' => 'ufru_max_file_size',
            ]
        );
        

        // Folder scan functionality
        register_setting(
            'ufru_folder_scan', // Option group
            'ufru_folder_scan', // Option name
        );

        add_settings_section(
            'settings_section_folder_scan', // ID
            'Folder Scan', // Title
            null, // Callback
            'ufru-settings-admin-folder-scan' // Page
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = [];
        if( isset( $input['ufru_max_number_of_uploads'] ) )
            $new_input['ufru_max_number_of_uploads'] = absint( $input['ufru_max_number_of_uploads'] );

        if( isset( $input['ufru_allowed_file_types'] ) )
            $new_input['ufru_allowed_file_types'] = sanitize_text_field( $input['ufru_allowed_file_types'] );

        if( isset( $input['ufru_max_file_size'] ) && (int)$input['ufru_max_file_size'] == $input['ufru_max_file_size'] ) {
            $new_input['ufru_max_file_size'] = $input['ufru_max_file_size'];
        } else {
            $errorMsg = '<div class="error notice">' . __('Error: Max file size is not a number.', 'uploads-for-registered-users') .  '</div>';
            wp_die($errorMsg);
        }
            

        return $new_input;
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function input_max_number_of_uploads_callback() {
        printf(
            '<input type="number" id="ufru_max_number_of_uploads" name="ufru_settings[ufru_max_number_of_uploads]" value="%s" />',
            (isset($this->options['ufru_max_number_of_uploads']) && $this->options['ufru_max_number_of_uploads']) ? esc_attr( $this->options['ufru_max_number_of_uploads']) : 10
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function input_allowed_formats_callback() {
        $tip = '<br><p>' . __('Enter file extensions separated by space, for example: ', 'uploads-for-registered-users') . ' <code>jpg jpeg png</code></p>';
        printf(
            '<input type="text" id="ufru_allowed_file_types" name="ufru_settings[ufru_allowed_file_types]" value="%s" />',
            (isset( $this->options['ufru_allowed_file_types'] ) && !empty($this->options['ufru_allowed_file_types'])) ? esc_attr( $this->options['ufru_allowed_file_types']) : 'jpg jpeg png'
        );
        print($tip);
    }

    public function input_max_file_size_callback() {
        $value = (isset( $this->options['ufru_max_file_size'] ) && !empty($this->options['ufru_max_file_size'])) ? $this->options['ufru_max_file_size'] : 2097152;
        $value = intval($value); // Ensure the value is an integer representing megabytes.
        $tip = '<br><p>' . __('Value should be represented in bytes, for example:', 'uploads-for-registered-users') . ' <code>2097152 = 2MB</code></p>';

        printf(
            '<input type="number" id="ufru_max_file_size" name="ufru_settings[ufru_max_file_size]" max="'. ufru_get_file_max_upload_size() .'" value="%d" />',
            esc_attr($value)
        );
        print($tip);
    }
}

if( is_admin() )
    $my_settings_page = new UFRUSettingsPage();
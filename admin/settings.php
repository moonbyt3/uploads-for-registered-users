<?php
class UFRUSettingsPage {
    private $options;

    public function __construct() {
        add_action('admin_menu', [$this, 'add_page_settings'], 9999);
        add_action('admin_init', [$this, 'page_init']);
    }

    /**
     * Add options page
     */
    public function add_page_settings() {
        add_submenu_page(
            'uploads-for-registered-users',
            'Settings',
            'Settings',
            'manage_options',
            'ufru-settings-admin',
            [$this, 'create_admin_page']
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

        add_settings_field(
            'ufru_allowed_roles_to_upload_files', // ID
            'Allowed users', // Title
            [$this, 'allowed_users_callback'], // Callback
            'ufru-settings-admin', // Page
            'settings_section_general', // Section
            [
                'name' => 'ufru_allowed_roles_to_upload_files',
                'label_for' => 'ufru_allowed_roles_to_upload_files',
            ]
        );

        add_settings_section(
            'settings_section_user_files', // ID
            'User Files settings', // Title
            null, // Callback
            'ufru-settings-admin' // Page
        );

        add_settings_field(
            'ufru_user_files_users_per_page', // ID
            'Users per page', // Title
            [$this, 'input_user_files_users_per_page_callback'], // Callback
            'ufru-settings-admin', // Page
            'settings_section_user_files', // Section
            [
                'name' => 'ufru_user_files_users_per_page',
                'label_for' => 'ufru_user_files_users_per_page',
            ]
        );

        $all_options = get_option( 'ufru_settings' );

        if (!isset($all_options['ufru_allowed_roles_to_upload_files'])) {
            $all_options['ufru_allowed_roles_to_upload_files'] = [
                'subscriber'
            ];

            update_option('ufru_settings', $all_options);
        }

        if (!isset($all_options['ufru_user_files_users_per_page'])) {
            $all_options['ufru_user_files_users_per_page'] = 8;

            update_option('ufru_settings', $all_options);
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = [];
        if ( isset( $input['ufru_max_number_of_uploads'] ) )
            $new_input['ufru_max_number_of_uploads'] = absint( $input['ufru_max_number_of_uploads'] );

        if ( isset( $input['ufru_allowed_file_types'] ) )
            $new_input['ufru_allowed_file_types'] = sanitize_text_field( $input['ufru_allowed_file_types'] );

        if ( isset( $input['ufru_max_file_size'] ) && (int)$input['ufru_max_file_size'] == $input['ufru_max_file_size'] ) {
            $new_input['ufru_max_file_size'] = sanitize_text_field( $input['ufru_max_file_size'] );
        } else {
            $errorMsg = '<div class="error notice">' . __('Error: Max file size is not a number.', 'uploads-for-registered-users') .  '</div>';
            wp_die($errorMsg);
        }
        if ( isset( $input['ufru_allowed_roles_to_upload_files'] ) )
            $new_input['ufru_allowed_roles_to_upload_files'] = $input['ufru_allowed_roles_to_upload_files'];
        if ( isset( $input['ufru_user_files_users_per_page']) )
            $new_input['ufru_user_files_users_per_page'] = sanitize_text_field( $input['ufru_user_files_users_per_page'] );

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

    public function allowed_users_callback() {
        $selected_roles = $this->options['ufru_allowed_roles_to_upload_files'];
        $all_roles = get_editable_roles();
        ?>
            <div>
                <?php foreach ($all_roles as $role_key => $role_data) : ?>
                    <label for="role_<?php echo esc_attr($role_key); ?>">
                        <input
                            type="checkbox"
                            id="role_<?php echo esc_attr($role_key); ?>"
                            name="ufru_settings[ufru_allowed_roles_to_upload_files][]"
                            value="<?php echo esc_attr($role_key); ?>"
                            <?php echo (in_array($role_key, $selected_roles) ? 'checked' : ''); ?>
                        /> 
                        <?php echo esc_html($role_data['name']); ?>
                    </label>
                    <br>
                <?php endforeach; ?>
            </div>
        <?php
    }

    public function input_user_files_users_per_page_callback() {
        printf(
            '<input type="number" id="ufru_user_files_users_per_page" name="ufru_settings[ufru_user_files_users_per_page]" value="%s" />',
            (isset($this->options['ufru_user_files_users_per_page']) && $this->options['ufru_user_files_users_per_page']) ? esc_attr( $this->options['ufru_user_files_users_per_page']) : 8
        );
    }
}

if( is_admin() )
    $my_settings_page = new UFRUSettingsPage();

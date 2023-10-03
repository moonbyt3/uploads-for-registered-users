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

            <div class="wrap">
                <h2>Debug</h2>
                <p>
                    <?php 
                        $maxFileUploads = ini_get('max_file_uploads'); 
                        $clientMaxBodySize = ini_get('client_max_body_size');
                        $debugOptions = get_option( 'ufru_max_number_of_uploads' );
                    ?>
                    Server Max File Uploads: <?php echo $maxFileUploads; ?>
                    <br>
                    Server Client Max Body Size: <?php echo $clientMaxBodySize; ?>
                    <br>
                    Debug options: <?php echo $debugOptions; ?>
                </p>
                <br><br>
                <p>TODO: Scan for deleted users</p>
                <form method="post">
                    <button type="submit">Scan</button>
                </form>
            </div>
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
        $tip = '<br><p>' . __('Enter file extensions separated by space, for example: ', 'uploads-for-registered-users') . '<code>jpg jpeg png</code></p>';
        printf(
            '<input type="text" id="ufru_allowed_file_types" name="ufru_settings[ufru_allowed_file_types]" value="%s" />',
            (isset( $this->options['ufru_allowed_file_types'] ) && !empty($this->options['ufru_allowed_file_types'])) ? esc_attr( $this->options['ufru_allowed_file_types']) : 'jpg jpeg png'
        );
        print($tip);
    }
}

if( is_admin() )
    $my_settings_page = new UFRUSettingsPage();
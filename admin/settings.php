<?php
function settings_page() {
	add_submenu_page(
		'uploads-for-registered-users',
		'Settings',
		'Settings',
		'manage_options',
		'settings',
		'settings_screen',
        100
	);
}
add_action( 'admin_menu', 'settings_page' );

function ufru_register_settings() {
    register_setting('ufru-max-number-of-uploads', 'ufru_max_number_of_uploads');
}
add_action('admin_init', 'ufru_register_settings');

function calculate_max_number_of_uploads() {
    $server_max_file_uploads = ini_get('max_file_uploads');
    $plugin_max_file_uploads = esc_attr(get_option('ufru_max_number_of_uploads'));
    $result = 0;

    if ((int)$plugin_max_file_uploads <= (int)$server_max_file_uploads) {
        $result = (int)$plugin_max_file_uploads;
    } else {
        $result = (int)$server_max_file_uploads;
    }

    return $result;
}

function settings_screen() {
    ?>
        <div class="wrap">
            <h1>My Plugin Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('ufru-max-number-of-uploads'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Max number of user uploads</th>
                        <td>
                            <input 
                                type="number"
                                name="ufru_max_number_of_uploads" 
                                id="ufru_max_number_of_uploads"
                                value="<?php echo calculate_max_number_of_uploads(); ?>"
                                min="1"
                                max="<?php echo ini_get('max_file_uploads'); ?>"
                            />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <div class="wrap">
            <h2>Debug</h2>
            <p>
                <?php $maxUploads = ini_get('max_file_uploads'); ?>
                INI Max File Uploads: <?php echo $maxUploads; ?>
            </p>
            <br><br>
            <p>TODO: Scan for deleted users</p>
            <form method="post">
                <button type="submit">Scan</button>
            </form>
        </div>
    <?php
}
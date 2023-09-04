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
    register_setting('ufru-max-number-of-uploads', 'ufru_max_number_of_uploads', [
        'default' => '10'
    ]);
    if ( get_option( 'ufru_max_number_of_uploads' ) === false ) {
        update_option( 'ufru_max_number_of_uploads', '10' );
    }
}
add_action('admin_init', 'ufru_register_settings');

function settings_screen() {
    ?>
        <div class="wrap">
            <h1>My Plugin Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('ufru-max-number-of-uploads'); ?>
                <?php do_settings_sections('my-plugin-settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Max number of user uploads</th>
                        <td>
                            <?php echo get_option('ufru_max_number_of_uploads'); ?>
                            <input 
                                type="text"
                                name="ufru_max_number_of_uploads" 
                                value="<?php echo esc_attr(get_option('ufru_max_number_of_uploads', '10')); ?>"
                                value="<?php echo esc_attr(get_option('ufru_max_number_of_uploads')); ?>" 
                            />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <div class="wrap">
            <h2>Scan Folders</h2>
            <br><br>
            <p>TODO: Scan for deleted users</p>
            <form method="post">
                <button type="submit">Scan</button>
            </form>
        </div>
    <?php
}
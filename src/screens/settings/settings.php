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

function settings_screen() {
    ?>
        <div class="wrap">
            <h1>Settings</h1>
            <br><br>
            <p>TODO: Scan for deleted users</p>
            <form method="post">
                <button type="submit">Scan</button>
            </form>
        </div>
    <?php
}
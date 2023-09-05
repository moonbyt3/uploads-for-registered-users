<?php 
    function uploads_for_registered_users_activate() {
        // Add activation code here, such as setting default options
        add_option( 'ufru_max_number_of_uploads', 10, '', 'yes');
    }

    register_activation_hook(UFRU_PLUGIN_PATH, 'uploads_for_registered_users_activate');
?>
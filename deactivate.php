<?php 
    function uploads_for_registered_users_deactivate() {
        // Add deactivation code here, such as cleaning up or removing options
        
    }
    
    register_deactivation_hook(UFRU_PLUGIN_PATH, 'uploads_for_registered_users_deactivate');
?>
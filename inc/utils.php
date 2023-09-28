<?php
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
?>
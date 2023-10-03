<?php
    function urfu_calculate_max_number_of_uploads() {
        $plugin_max_file_uploads = esc_attr(get_option('ufru_max_number_of_uploads'));
        if ($plugin_max_file_uploads) {
            return (int)$plugin_max_file_uploads;
        } else {
            return 10;
        }
    }
?>
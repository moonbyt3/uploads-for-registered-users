<?php
    /**
     * This function calculates maximum number of uploads. 
     *
     * @return int Number of maximum user uploads
     **/
    function urfu_calculate_max_number_of_uploads() {
        $plugin_max_file_uploads = esc_attr(get_option( 'ufru_settings' )['ufru_max_number_of_uploads']);
        if ( $plugin_max_file_uploads ) {
            return (int) $plugin_max_file_uploads;
        } else {
            return 10;
        }
    }

    /**
     * This function returns the maximum files size that can be uploaded 
     * in PHP
     * @return int File size in bytes
     **/
    function ufru_get_file_max_upload_size() {
        $maxUploadSizeInBytes =    ufru_convert_PHP_size_to_bytes(ini_get('upload_max_filesize'));
        return ufru_convert_bytes_to_megabytes($maxUploadSizeInBytes);
    }

    /**
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     * 
     * @param string $sSize
     * @return int The value in bytes
     */
    function ufru_convert_PHP_size_to_bytes( $sSize ) {
        $sSuffix = strtoupper( substr( $sSize, -1 ) );

        if ( ! in_array( $sSuffix, array( 'P', 'T', 'G', 'M', 'K' ) ) ) {
            return (int) $sSize;
        }

        $iValue = substr( $sSize, 0, -1 );

        switch ( $sSuffix ) {
            case 'P':
                $iValue *= 1024;
            // Fallthrough intended
            case 'T':
                $iValue *= 1024;
            // Fallthrough intended
            case 'G':
                $iValue *= 1024;
            // Fallthrough intended
            case 'M':
                $iValue *= 1024;
            // Fallthrough intended
            case 'K':
                $iValue *= 1024;
                break;
        }

        return (int) $iValue;
    }

    /** 
     * This function converts bytes to megabytes
     *
     * @param int $bytes 
     * @return int 
     */
    function ufru_convert_bytes_to_megabytes($bytes) {
        if (!is_numeric($bytes)) {
            $errorMsg = '<div class="error notice">' . __('Error: Function parameter is not integer.', 'uploads-for-registered-users') .  '</div>';
            wp_die($errorMsg);
        } else {
            return round($bytes / 1024 / 1024, 2);
        }
    }

    /** 
     * This function removes file for fiven path
     *
     * @param string $user_id 
     * @param string $user_name
     * @param string $file_name
     * @return void 
     */
    function ufru_remove_file($user_id, $user_name, $file_name) {
        $plugin_name = 'ufru';
        $user_name = preg_replace('/\s+/', '_', $user_name);
        $file_name = sanitize_file_name($file_name);
        $filepath_to_delete = wp_upload_dir()['basedir'] . '/' . $plugin_name . '/' . $user_id . '_' . $user_name  . '/' . $file_name;

        if ( file_exists( $filepath_to_delete ) ) {
            unlink( $filepath_to_delete ); // Delete the file
        }
    }
?>
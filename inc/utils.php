<?php
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
        return min( ufru_convert_PHP_size_to_bytes( ini_get( 'post_max_size' ) ), ufru_convert_PHP_size_to_bytes( ini_get( 'upload_max_filesize' ) ) );
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
?>
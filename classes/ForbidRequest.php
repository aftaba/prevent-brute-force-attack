<?php

class ForbidRequest {

    public static function init() {
        add_action("wp_authenticate", [self::class,'check_and_forbid_request']);
    }


    /**
     * Check if IP is in blocked_ips then forbid the request
     */
    public static function check_and_forbid_request(){

        // check if table is created and record is updated in options table
        if( ! get_option("create_failed_logins_table",0) ) {
            return;
        }

        global $wpdb;
        $user_ip = LogAndBlockIP::get_client_ip();
        
        $table_name = DatabaseHelper::get_blocked_ips_table_name();
        
        $query = "SELECT user_ip from $table_name where user_ip = '$user_ip' LIMIT 1";
        $result = $wpdb->get_var($query);
        
        if( $result ) {
            http_response_code(429);
            die('Access Forbidden');
        }
    }
}


ForbidRequest::init();
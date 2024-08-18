<?php

class LogAndBlockIP {

    private const REQUEST_TIME = 60; // 60 second
    private const REQUEST_LIMIT = 3; // 10 request
    
    /**
     * Initialize the class and define all hooks
     */
    public static function init() {
        add_action("wp_login_failed",[self::class, 'log_failed_login']);
    }

    /**
     * Handle Loging and Blocking the failed login ips
     * 
     * Check if table is created
     * Log failed logins 
     * If it throttle then add to block list
     */
    public static function log_failed_login($username) {
        DatabaseHelper::_maybe_create_table();

        $ip = self::get_client_ip();
        self::log_failed_login_in_table($username, $_POST['pwd'], $ip );
        self::check_and_add_to_block_list($ip);
        
    }

    /**
     * Log the failed login details into "failed_logins" table
     * 
     * @param $username
     * @param $password
     * @param $ip
     */
    public static function log_failed_login_in_table($username, $password, $ip ){
        global $wpdb;
        $table_name = DatabaseHelper::get_failed_logins_table_name();
        
        $wpdb->insert($table_name,  array(
            'username' => $username,
            'password' => $password,
            'user_ip' => $ip,
            'last_attempt' => date_i18n("Y-m-d H:i:s")
        ));
    }

    /**
     * Check if the request is throttling then add to block_list table
     * 
     * @param $ip
     */
    public static function check_and_add_to_block_list( $ip ) {
        global $wpdb;
        $table_name = DatabaseHelper::get_failed_logins_table_name();
        
        $time_to_check = date_i18n("Y-m-d H:i:s", current_time('timestamp') - self::REQUEST_TIME );

        //@todo use preprare
        $query = "SELECT COUNT(*) as request_count FROM $table_name  WHERE last_attempt >= '$time_to_check' AND user_ip = '$ip'";
        $result = $wpdb->get_var($query);
        
        if( $result && $result > self::REQUEST_LIMIT  ) {
            self::add_current_ip_to_block_list($ip); 
        }
    }

    
    /**
     * Add IP to blocked_ips table
     * 
     * @param $ip - the IP to add to blocked_ips table
     */
    public static function add_current_ip_to_block_list($ip) {
        global $wpdb;

        $table_name = DatabaseHelper::get_blocked_ips_table_name();

        $wpdb->insert($table_name,  array(
            'user_ip' => $ip,
            'blocked_at' => date_i18n("Y-m-d H:i:s"),
        ));
    }

    /** 
     * Get user IP
     */
    public static function get_client_ip() {
        
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}


LogAndBlockIP::init();
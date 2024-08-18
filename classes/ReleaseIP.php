<?php

class ReleaseIP {

    private const BLOCKED_IP_TIME = 600; // 10 minute

    public static function init() {
        //@todo  clear ip using cron scheduler
        add_action("init",[self::class, 'maybe_clear_ip']);
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
        //@todo - use wpdb->select
        $query = "SELECT user_ip from $table_name where user_ip = '$user_ip' LIMIT 1";
        $result = $wpdb->get_var($query);
        
        if( $result ) {
            http_response_code(429);
            die('Access Forbidden');
        }
    }

    /** 
     * Check if BLOCK_IP_TIME is passed then remove the record from database
     */
    public static function maybe_clear_ip() {
        
        global $wpdb;
        $table_name = DatabaseHelper::get_blocked_ips_table_name();
        $time_to_check = date_i18n("Y-m-d h:i:s", current_time('timestamp') - self::BLOCKED_IP_TIME );

        $query = "SELECT user_ip from $table_name WHERE blocked_at <= '$time_to_check' LIMIT 10";
        
        $results = $wpdb->get_results($query, ARRAY_A);
        $copy_results = $results;

        if( count($results) > 0 ) {
            
            $delete_ip = "(";

            foreach($results as $result) {
                $ip = $result['ip'];
                if( next($copy_results)) {
                    $delete_ip .= "'$ip',";
                } else {
                    $delete_ip .= "'$ip'";
                }
            }

            $delete_ip .= ")";

            //@todo using wpdb->delete
            $query = "DELETE FROM $table_name WHERE ip IN $delete_ip";
            $wpdb->query($query);    
        }

        
    }
}


ReleaseIP::init();
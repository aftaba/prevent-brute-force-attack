<?php 

class UninstallPlugin{
    
    public static function init(){
        register_uninstall_hook(__FILE__, [self::class, 'handle_uninstall']);

        //@todo - drop table if site is deleted.

    }

    public static function handle_uninstall(){
        
        // check if multisite 
        self::delete_blocked_ips_table();
        self::delete_failed_logins_table();
        
    }

    /**
     * Delete table and 
     */
    public static function delete_failed_logins_table(){
        global $wpdb;
        $table_name = DatabaseHelper::get_failed_logins_table_name();
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
        delete_option('create_failed_logins_table');
    }

    public static function delete_blocked_ips_table(){
        global $wpdb;
        $table_name = DatabaseHelper::get_blocked_ips_table_name();
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
        delete_option('create_blocked_ips_table');
    }
}


UninstallPlugin::init();
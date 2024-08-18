<?php

class DatabaseHelper {


    /**
     * Get the blocked_ips tablename
     */
    public static function get_blocked_ips_table_name(){
        global $wpdb;
        return $wpdb->prefix."blocked_ips";
    }

    /**
     * Get the failed_logins tablename
     */
    public static function get_failed_logins_table_name(){
        global $wpdb;
        return $wpdb->prefix."failed_logins";
    }

    /**
     * Check if tables are not created then created it.
     * Handled this way for multisite setup.
     */
    public static function _maybe_create_table()  {

        if( ! get_option("create_failed_logins_table",0) ) {
            self::create_failed_logins_table();
        } 

        if( ! get_option("create_blocked_ips_table",0) ) {
            self::create_blocked_ips_table();
        }
    }

    /**
     * Create table failed_logins if not exist.
     */
    public static function create_failed_logins_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = self::get_failed_logins_table_name();
        
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` bigint(20) NOT NULL auto_increment,
            `username`  varchar(100) NOT NULL,
            `password` varchar(100) NOT NULL,
            `user_ip` varchar(16) NOT NULL,
            `last_attempt` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        $wpdb->query($sql);
        update_option("create_failed_logins_table", 1);
    }

    /**
     * Create table blocked_ips if not exist.
     */
    public static function create_blocked_ips_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = self::get_blocked_ips_table_name();

        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` bigint(20) NOT NULL auto_increment,
            `user_ip` varchar(16) NOT NULL DEFAULT '',
            `blocked_at` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        $wpdb->query($sql);
        update_option("create_blocked_ips_table", 1);
    }
}
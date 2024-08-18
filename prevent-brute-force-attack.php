<?php 
/**
 * Query Monitor plugin for WordPress
 *
 * @package   prevent-brute-force-attack
 * @link      https://github.com/aftaba/prevent-brute-force-attack
 * @author    Aftab Alam
 * @license   GPL v2 or later
 *
 * Plugin Name:  Prevent Brute Force Attack
 * Description:  Helps in preventing brute force attack to all login request.
 * Version:      3.16.3
 * Plugin URI:   https://wordpress.org/
 * Author:       Aftab Alam
 * Author URI:   https://profiles.wordpress.org/aftabalam8028/
 * Text Domain:  prevent-brute-force-attack
 * Requires at least: 5.7
 * Requires PHP: 7.4
 * License URI:  https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * License:      GPL v2 or later
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PLUGIN_VERSION', '1.0.0' );


require_once "classes/DatabaseHelper.php";
require_once "classes/LogAndBlockIP.php";
require_once "classes/ReleaseIP.php";
require_once "classes/ForbidRequest.php";
require_once "classes/UninstallPlugin.php";
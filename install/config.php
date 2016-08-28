<?php
/**
 * IkoBB configuration file.
 * IkoBB (http://ikobb.de)
 */

if (!defined('BASEPATH')) {
    die();
}


define('MYSQL_HOST', '%mysql_host%');  // Your SQL host                             Default: localhost
define('MYSQL_USERNAME', '%mysql_username%'); // Your SQL username
define('MYSQL_PASSWORD', '%mysql_password%'); // Your SQL password
define('MYSQL_DATABASE', '%mysql_database%'); // Your SQL Database for IkoBB
define('MYSQL_PREFIX', '%mysql_prefix%'); // The prefix for this forum              Default: iko_
define('MYSQL_PORT', 3306); // The port to your SQL                                 Default: 3306

/**
 * Iko local details
 */
define('SITE_URL', '%site_url%'); // Without the ending "/"
define('IKOBB_VERSION', '0.1.0'); // Do not change if you want to get updates       Default: 0.1.0
define('IKO_SESSION_TIMEOUT', 31536000); // In seconds.                             Default: 31536000 (one year)
define('USER_PASSWORD_HASH_COST', 10);  // Used for password_hash() function;       Default: 10

/**
 * User group details.
 * DO NOT CHANGE IF YOU DON'T KNOW WHAT THIS WILL DO.
 */
define('ADMIN_ID', '4'); // ID of Admin group                                       Default: 4
define('MOD_ID', '3'); // ID of moderator group                                     Default: 3
define('BAN_ID', '2'); // ID of banned user group                                   Default: 2

/**
 * Forum configuration.
 */
define('THREAD_RESULTS_PER_PAGE', 20); // Number of threads per page                Default: 20
define('POST_RESULTS_PER_PAGE', 10); // Number of post per page                     Default: 10

?>

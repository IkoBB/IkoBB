<?php
/**
 * @author Marcel
 *
 */
namespace Iko;

use \PDO as PDO;

error_reporting(E_ALL);

/**
 * @author Marcel
 *
 */
class Core
{
    public static $basepath;
    public static $adminpath;
    public static $corepath;
    public static $modulepath;
    public static $PDO;

    /**
     * @param int $phase
     */
    public static function init($phase)
    {
        switch ($phase) {
            case 0:
                self::$basepath = self::loadPath();
                self::$corepath = self::$basepath . "core/";
                self::$adminpath = self::$basepath . "admin/";
                self::$modulepath = self::$basepath . "module/";
                break;
            case 1:
                self::loadPDO();
                break;
            default:
                null;
                break;
        }
    }

    /**
     * @return string
     */
    private static function loadPath()
    {
        $file = dirname(__FILE__);
        $win = false;
        if (strpos($file, "\\") > 0) {
            $win = true;
        }
        if ($win == true) {
            $file = strtolower(str_replace("\\", "/", $file));
        }
        $base = str_replace(strtolower($_SERVER['DOCUMENT_ROOT']), "", $file);
        $base = str_replace("/core", "", $base);
        $base = str_replace($base, "", strtolower($_SERVER['PHP_SELF']));
        $base = explode("/", $base);
        $dir = "./";
        if (count($base) > 1) {
            for ($i = 0; $i < count($base); $i++) {
                if ($base[$i] != "" && $i > 1) {
                    $dir .= "../";
                }
            }
        }
        return $dir;
    }

    /**
     * Load PDO with /Core/Database.conf.php file over Class Config
     */
    private static function loadPDO()
    {
        $config = config::load("file", "core/database.conf.php");
        self::$PDO = new PDO($config->get("dns"), $config->get("username"), $config->get("password"), $config->get("options"));
    }
}

/**
 *  Load Phase 1
 */

Core::init(0);

/**
 *  Load Config Loader
 */

require_once 'config.class.php';

/**
 *  Load Phase 2
 */

Core::init(1);
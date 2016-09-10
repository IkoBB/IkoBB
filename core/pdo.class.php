<?php
/**
 * @author Marcel
 *
 */
namespace Iko;

use \PDO as DB;

/**
 * @author Marcel
 *
 */
class PDO extends DB
{
    /**
     * {@inheritDoc}
     * @see PDO::query()
     */
    public function query(string $statement)
    {
        $config = config::load("file", "core/database.conf.php");
        $statement = str_replace("{prefix}", $config->get("prefix"), $statement);
        parent::query($statement);
    }
}
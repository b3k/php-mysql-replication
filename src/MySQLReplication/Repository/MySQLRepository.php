<?php

namespace MySQLReplication\Repository;

use Doctrine\DBAL\Connection;

/**
 * Class MySQLRepository
 * @package MySQLReplication\Repository
 */
class MySQLRepository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * @param string $schema
     * @param string $table
     * @return array
     */
    public function getFields($schema, $table)
    {
        $sql = '
            SELECT
                `c`.`COLUMN_NAME`,
                `c`.`COLLATION_NAME`,
                `c`.`CHARACTER_SET_NAME`,
                `c`.`COLUMN_COMMENT`,
                `c`.`COLUMN_TYPE`,
                `c`.`COLUMN_KEY`,
                `kcu`.`REFERENCED_TABLE_NAME`,
                `kcu`.`REFERENCED_COLUMN_NAME`
            FROM
                `information_schema`.`columns` AS `c`
            LEFT JOIN
                (SELECT
                    `REFERENCED_TABLE_NAME`,
                    `REFERENCED_COLUMN_NAME`,
                    `COLUMN_NAME`,
                    `TABLE_SCHEMA`,
                    `TABLE_NAME`
                FROM
                    `information_schema`.`key_column_usage`
                WHERE
                    `table_schema` = ?
                    AND `table_name` = ?
                    AND `referenced_table_schema` IS NOT NULL
                    AND `referenced_table_name` IS NOT NULL
                ) AS `kcu`
            USING (`column_name`, `table_schema`, `table_name`)
            WHERE
                `c`.`table_schema` = ?
                AND `c`.`table_name` = ?
       ';

        return $this->getConnection()->fetchAll($sql, [$schema, $table, $schema, $table]);
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        if (false === $this->connection->ping()) {
            $this->connection->close();
            $this->connection->connect();
        }

        return $this->connection;
    }

    /**
     * @return bool
     */
    public function isCheckSum()
    {
        $res = $this->getConnection()->fetchAssoc('SHOW GLOBAL VARIABLES LIKE "BINLOG_CHECKSUM"');

        return isset($res['Value']);
    }

    /**
     * File
     * Position
     * Binlog_Do_DB
     * Binlog_Ignore_DB
     * Executed_Gtid_Set
     *
     * @return array
     */
    public function getMasterStatus()
    {
        return $this->getConnection()->fetchAssoc('SHOW MASTER STATUS');
    }
}

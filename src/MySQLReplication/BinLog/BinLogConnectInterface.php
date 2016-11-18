<?php
/**
 * Created by PhpStorm.
 * User: Maciej Mroziński <maciej.mrozinski@kinguin.net>
 * Date: 18.11.16
 * Time: 10:43
 */

namespace MySQLReplication\BinLog;


use MySQLReplication\BinLog\Exception\BinLogException;
use MySQLReplication\MySQLReplicationFactory;

interface BinLogConnectInterface
{
    /**
     * @return bool
     */
    public function isConnected();

    /**
     * @return bool
     */
    public function getCheckSum();

    /**
     * @param MySQLReplicationFactory $factory
     * @throws BinLogException
     */
    public function connectToStream(MySQLReplicationFactory $factory);

    /**
     * @param bool $checkForOkByte
     * @return string
     * @throws BinLogException
     */
    public function getPacket($checkForOkByte = true);

    /**
     * @param $packet
     * @return array
     * @throws BinLogException
     */
    public function isWriteSuccessful($packet);
}
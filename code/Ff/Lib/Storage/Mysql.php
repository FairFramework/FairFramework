<?php

namespace Ff\Lib\Storage;

use Ff\Lib\Bus;

use Ff\Lib\Data;
use Ff\Lib\Storage\Mysql\Query;
use Ff\Lib\Storage\Mysql\Query\Builder;

class Mysql
{
    /**
     *
     * @var Bus
     */
    private $bus;
    
    protected $connection;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct(Bus $bus, Data $configuration)
    {
        $this->bus = $bus;

        $driverOptions = $configuration->get('attributes/connection/driver_options');

        $options = array();
        if (!empty($driverOptions)) {
            foreach ($driverOptions as $option) {
                $options[$option->code] = $option->value;
            }
        }

        $this->connection = new \PDO(
            $configuration->get('attributes/connection/dsn'),
            $configuration->get('attributes/connection/username'),
            $configuration->get('attributes/connection/password'),
            $options
        );
    }

    public function getQuery()
    {
        return new Query();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function execute(Query $query, $queryType)
    {
        $binds = $this->getBinds($query);

        try {
            $sql = Builder::toQueryString($query, $this);

            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new \Exception('Wrong query: ' . $sql);
            }

            $stmt->execute($binds);

            $result = null;

            switch ($queryType) {
                case Query::TYPE_FETCH_ONE:
                    $result = $this->_fetchOne($stmt);
                    break;
                case Query::TYPE_FETCH_ROW:
                    $result = $this->_fetchRow($stmt);
                    break;
                case Query::TYPE_FETCH_COLUMN:
                    $result = $this->_fetchColumn($stmt);
                    break;
                case Query::TYPE_FETCH_ALL:
                    $result = $this->_fetchAll($stmt);
                    break;
                case Query::TYPE_FETCH_ASSOC:
                    $result = $this->_fetchAssoc($stmt);
                    break;
                case Query::TYPE_FETCH_PAIRS:
                    $result = $this->_fetchPairs($stmt);
                    break;
                case Query::TYPE_INSERT:
                    $result = $this->lastInsertId();
                    break;
                case Query::TYPE_UPDATE:
                case Query::TYPE_DELETE:
                case Query::TYPE_ALTER:
                    $result = true;
                    break;
            }

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    ////////////////////////////////////////////////////////////////////////////

    public function getBinds(Query $query)
    {
        $binds = $query->binds();
        if (is_array($binds)) {
            foreach ($binds as $name => $value) {
                if (!is_int($name) && !preg_match('/^:/', $name)) {
                    $newName = ":$name";
                    unset($binds[$name]);
                    $binds[$newName] = $value;
                }
            }
        }
        return $binds;
    }

    public function quote($var)
    {
        if (is_array($var)) {
            $quote = array();
            foreach ($var as $v) {
                $quote[] = $this->quote($v);
            }
            return implode(',', $quote);
        } elseif (is_int($var)) {
            return $var;
        } elseif (is_float($var)) {
            return sprintf('%F', $var);
        } elseif (is_string($var)) {
            return "'" . $this->addSlashes($var) . "'";
        } elseif (null === $var) {
            return "NULL";
        }
    }

    public function addSlashes($var)
    {
        return addcslashes($var, "\000\n\r\\'\"\032");
    }

    protected function _fetchOne($stmt)
    {
        $data = $stmt->fetch(2);
        if ($data) {
            return reset($data);
        } else {
            return null;
        }
    }

    protected function _fetchRow($stmt)
    {
        return $stmt->fetch(2);
    }

    protected function _fetchColumn($stmt)
    {
        $data = array();
        while ($row = $stmt->fetch(3)) {
            $data[] = $row[0];
        }
        return $data;
    }

    protected function _fetchAll($stmt)
    {
        return $stmt->fetchAll(2);
    }

    protected function _fetchAssoc($stmt)
    {
        $data = array();
        while ($row = $stmt->fetch(2)) {
            $tmp = array_values(array_slice($row, 0, 1));
            $data[$tmp[0]] = $row;
        }
        return $data;
    }

    protected function _fetchPairs($stmt)
    {
        $data = array();
        while ($row = $stmt->fetch(3)) {
            $data[$row[0]] = $row[1];
        }
        return $data;
    }
}

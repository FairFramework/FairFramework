<?php

namespace Ff\Lib\Storage\Mysql;

use Ff\Lib\Storage\Mysql;
use Ff\Runtime\Storage;

class Query
{
    const TYPE_INSERT = 'insert';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';
    const TYPE_ALTER = 'alter';
    const TYPE_TRUNCATE = 'truncate';
    const TYPE_FETCH_ONE = 'fetch_one';
    const TYPE_FETCH_ROW = 'fetch_row';
    const TYPE_FETCH_ALL = 'fetch_all';
    const TYPE_FETCH_ASSOC = 'fetch_assoc';
    const TYPE_FETCH_COLUMN = 'fetch_column';
    const TYPE_FETCH_PAIRS = 'fetch_pairs';
    const TYPE_VALIDATE = 'validate';

    protected $_meta;
    protected $_table;
    protected $_fields = array();
    protected $_where = array();
    protected $_binds = array();

    protected $_order = array();
    protected $_limit = array();
    protected $_offset = array();
    protected $_group = array();

    public function __construct()
    {
        //
    }

    public function table($table = null)
    {
        if (null === $table) {
            return $this->_table;
        }

        $this->_table = $table;
    }

    public function fields($fields = null)
    {
        if (null === $fields) {
            return $this->_fields;
        }
        
        if ('*' === $fields) {
            $this->_fields = $fields;
        } elseif ('*' !== $this->_fields) {
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $this->_fields[$field] = $field;
                }
            } else {
                $this->_fields[$fields] = $fields;
            }
        }
    }

    public function where($where = null)
    {
        if (null === $where) {
            return $this->_where;
        }

        $this->_where[] = $where;
    }

    public function binds($binds = null)
    {
        if (null === $binds) {
            return $this->_binds;
        }

        $this->_binds = array_merge($this->_binds, $binds);
    }

    public function order($order = null)
    {
        if (null === $order) {
            return $this->_order;
        }

        $this->_order = array_merge($this->_order, $order);
    }

    public function limit($limit = null)
    {
        if (null === $limit) {
            return $this->_limit;
        }

        $this->_limit = $limit;
    }

    public function offset($offset = null)
    {
        if (null === $offset) {
            return $this->_offset;
        }

        $this->_offset = $offset;
    }

    public function group(array $group = null)
    {
        if (null === $group) {
            return $this->_group;
        }

        $this->_group = array_merge($this->_group, $group);
    }

    public function fetchRow()
    {
        return Storage::mysql()->execute($this, self::TYPE_FETCH_ROW);
    }
}
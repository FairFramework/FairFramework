<?php

namespace Ff\Lib\Storage\Mysql\Query;

use Ff\Lib\Storage\Mysql\Query;
use Ff\Lib\Storage\Mysql;

class Builder
{
    public static function toQueryString(Query $query, Mysql $adapter)
    {
        $sql = '';
        $sql .= self::fields($query->fields(), $adapter);
        $sql .= self::table($query->table(), $adapter);
        $sql .= self::where($query->where(), $adapter);
        $sql .= self::group($query->group(), $adapter);
        $sql .= self::limit($query->limit(), $adapter);
        $sql .= self::order($query->order(), $adapter);

        return $sql;
    }

    protected static function fields($fields)
    {
        $cols = array();
        if (is_string($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $field) {
            if ('*' !== $field) {
                $field = "`{$field}`";
            }
            $cols[] = $field;
        }
        return "SELECT " . implode(", ", $cols);
    }

    protected static function table($table)
    {
        return "\n    FROM `{$table}`";
    }

    protected static function where(array $wheres, Mysql $adapter)
    {
        $result = array();

        if ($wheres) {
            foreach ($wheres as $where) {
                foreach ($where as $left => $right) {
                    $result[] = self::assembleCondition($left, $right, $adapter);
                }
            }
        }
        
        return "\n    WHERE " . implode(" AND ", $result);
    }

    protected static function assembleCondition($left, $right, Mysql $adapter)
    {
        $condition = '';

        $left = "`{$left}`";
        if (is_array($right)) {
            $oper = key($right);
            $right = reset($right);

            switch ($oper) {
                case 'in':
                    $right = $adapter->quote($right);
                    $condition = "$left IN({$right})";
                    break;
                case 'notin':
                    $right = $adapter->quote($right);
                    $condition = "$left NOT IN({$right})";
                    break;
                case 'like':
                    $right = $adapter->addSlashes($right);
                    $condition = "$left LIKE '{$right}'";
                    break;
                case '%like':
                    $right = $adapter->addSlashes($right);
                    $condition = "$left LIKE '%{$right}'";
                    break;
                case 'like%':
                    $right = $adapter->addSlashes($right);
                    $condition = "$left LIKE '{$right}%'";
                    break;
                case '%like%':
                    $right = $adapter->addSlashes($right);
                    $condition = "$left LIKE '%{$right}%'";
                    break;
                case 'gt':
                    $right = $adapter->quote($right);
                    $condition = "$left > {$right}";
                    break;
                case 'get':
                    $right = $adapter->quote($right);
                    $condition = "$left >= {$right}";
                    break;
                case 'lt':
                    $right = $adapter->quote($right);
                    $condition = "$left < {$right}";
                    break;
                case 'let':
                    $right = $adapter->quote($right);
                    $condition = "$left <= {$right}";
                    break;
                case 'btw':
                    $r1 = $adapter->quote($right[0]);
                    $r2 = $adapter->quote($right[1]);
                    $condition = "$left BETWEEN {$r1} AND {$r2}";
                    break;
                case 'or':
                    $r = array();
                    foreach ($right as $part) {
                        $r[] = self::assembleCondition($left, $part, $adapter);
                    }
                    $condition = '(' . implode(' OR ', $r) . ')';
                    break;
            }
        } else {
            if (null === $right) {
                $condition = "$left IS NULL";
            } else {
                $right = $adapter->quote($right);
                $condition = "$left = {$right}";
            }
        }
        return $condition;
    }

    protected static function group(array $groups)
    {
        if (empty($groups)) {
            return '';
        }
        return "\n    GROUP BY " . implode(", ", $groups);
    }

    protected static function limit(array $limit)
    {
        if (empty($limit)) {
            return '';
        }
        return "\n    LIMIT " . $limit;
    }

    protected static function order(array $orders)
    {
        if (empty($orders)) {
            return '';
        }
        return "\n    ORDER BY " . implode(", ", $orders);
    }
}

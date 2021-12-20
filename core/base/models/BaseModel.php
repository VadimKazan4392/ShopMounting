<?php

namespace core\base\models;

use core\base\controllers\Singletone;
use core\base\exceptions\DBException;
use mysqli;

class BaseModel
{
    use Singletone;

    protected $db;

    private function __construct()
    {
        $this->db = @new mysqli(HOST, USER, PASS, DB_NAME);

        if($this->db->connect_error) {
            throw new DBException('Ошибка подключения к базе данных'
            . $this->db->connect_errno . ' ' . $this->db->connect_error);
        }
        $this->db->query('SET NAMES UTF8');
    }

    /**
     * @param string $query    строка запроса
     */
    final public function query($query, $crud = 'r', $return_id = false)
    {
        $result = $this->db->query($query);

        if($this->db->affected_rows === -1) {
            throw new DBException('Ошибка в SQL-запросе'
            . $query . '-' . $this->db->errno . '' . $this->db->error);
        }

        switch($crud) {
            case 'r':
                if($result->num_rows) {
                    $res = [];
                    for($i = 0; $i < $result->num_rows; $i++) {
                        $res[] = $result->fetch_assoc();
                    }
                    return $res;
                }
                return false;
            case 'c':
                if($return_id) {
                    return $this->db->insert_id;
                }
                return true;
            case 'u':
                //заглушка
                return;
            case 'd':
                //заглушка
                return;
            default:
                return true;
        }
    }

    /**
     * 
     * @param string $table имя таблицы
     * @param array $set массив опций
     */
    final public function get(string $table, array $set = [])
    {
        $fields = $this->createFields($table, $set);
        $where = $this->createWhere($table, $set);

        if(!$where) {
            $new_where = true;
        } else {
            $new_where = false;
        }
        $join_arr = $this->createJoin($table, $set, $new_where);

        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];
        
        $fields = rtrim($fields, ',');
        $order = $this->createOrder($table, $set);
        $limit = $set['limit'] ? $set['limit'] : '';

        $query = "SELECT $fields FROM $table $join $where $order $limit";

        return $this->query($query);
    }

    /**
     * 
     * @param mixed $table
     * @param array $set
     */
    protected function createFields($table = false, array $set): string
    {
        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : ['*'];
        
        $table = $table ? $table . '.' : '';

        $fields = '';
        foreach($set['fields'] as $field) {
            $fields .= $table . $field . ',';
        }
        return $fields;
    }

    protected function createOrder($table = false, $set)
    {
        $table = $table ? $table . '.' : '';

        $order_by = '';

        if(is_array($set['order']) && !empty($set['order'])) {
            $set['order_direction'] = (is_array($set['order_direction']) && !empty($set['order_direction'])) 
                ? $set['order_direction'] 
                : ['ASC'];

            $direct_count = 0;
            $order_by = "ORDER BY ";
            foreach($set['order'] as $order) {
                if($set['order_direction'][$direct_count]) {
                    $order_direct = strtoupper(['order_direction'][$direct_count]);
                    $direct_count++;
                } else {
                    $order_direct = strtoupper(['order_direction'][$direct_count - 1]);
                }
                if(is_int($order)) {
                    $order_by .= $order . ' ' . $order_direct . ',';
                } else {
                    $order_by .= $table . $order . ' ' . $order_direct . ',';
                }  
            }
            $order_by = rtrim($order_by, ',');
        }
        return $order_by;
    }

    protected function createWhere($table = false, $set, $instraction = "WHERE")
    {
        $where = '';
        $table = $table ? $table . '.' : '';
        if(is_array($set['where']) && !empty($set['where'])) {
            $set['operand'] = (is_array($set['operand']) && !empty($set['operand'])) ? $set['operand'] : '=';
            $set['condition'] = (is_array($set['condition']) && !empty($set['condition'])) ? $set['condition'] : 'AND';
            $where = $instraction;
            $o_count = 0;
            $c_count = 0;
            foreach($set['where'] as $key => $item) {
                $where .= ' ';
                if($set['operand'][$o_count]) {
                    $operand = $set['operand'][$o_count];
                    $o_count++;
                } else {
                    $operand = $set['operand'][$o_count - 1];
                }
                if($set['condition'][$c_count]) {
                    $condition = $set['condition'][$c_count];
                    $c_count++;
                } else {
                    $condition = $set['condition'][$c_count - 1];
                }

                if($operand === 'IN' || $operand === 'NOT IN') {
                    if(is_string($item) && strpos($item , 'SELECT')) {
                        $in_str = $item;
                    } else {
                        if(is_array($item)) {
                            $temp_item = $item;
                        } else {
                            $temp_item = explode(',', $item);
                        }
                        $in_str = '';
                        foreach($temp_item as $v) {
                            $in_str .= "'" . trim($v) . "',";
                        }
                    }
                    $where .= $table . $key . ' ' . $operand . ' (' . trim($in_str, ',') . ') ' . $condition;
                } elseif (strpos($operand, 'LIKE') !== false) {
                    $like_template = explode('%', $operand);
                    foreach($like_template as $lt_key => $lt_val) {
                        if(!$lt_val) {
                            if(!$lt_key) {
                                $item = '%' . $item;
                            } else {
                                $item .= '%';
                            }
                        }
                    }
                    $where .= $table . $key . ' LIKE ' . "'" . $item . "' $condition";
                } else {
                    if(strpos($item, 'SELECT') === 0) {
                        $where .= $table . $key . $operand . '(' . $item . ") $condition";
                    } else {
                        $where .= $table . $key . $operand . "'" . $item . "' $condition";
                    }
                }
            }
            $where = substr($where, 0, strrpos($where, $condition));
        }
        return $where;
    }

    protected function createJoin($table, $set, $new_where = false) 
    {
        $fields = '';
        $join = '';
        $where ='';

        if($set['join']) {
            $join_table = $set['join'];
            foreach($set['join'] as $key => $item) {
                if(is_int($key)) {
                    if(!$item['table']) {
                        continue;
                    } else {
                        $key = $item['table'];
                    }
                }
                if($join) {
                    $join .= ' ';
                }
                if($item['on']) {
                    $join_fields = [];

                    switch(2) {
                        case count($item['on']['fields']):
                            $join_fields = $item['on']['fields'];
                            break;
                        case count($item['on']):
                            $join_fields = $item['on'];
                            break;
                        default:
                            continue(2);
                            break;
                    }
                    if(!$item['type']) {
                        $join .= 'LEFT JOIN';
                    } else {
                        $join .= trim(strtoupper($item['type'])) . ' JOIN ';
                    }
                    $join .= $key . ' ON ';

                    if($item['on']['table']) {
                        $join .= $item['on']['table'];
                    } else {
                        $join .= $join_table;
                    }
                    $join .= '.' . $join_fields[0] . '=' . $key . '.' . $join_fields[0];
                    $join_table = $key;

                    if($new_where) {
                        if($item['where']) {
                            $new_where = false;
                        }
                        $group_condition = 'WHERE';
                    } else {
                        $group_condition = $item['group_condition'] ? strtoupper($item['group_condition']) : 'AND';
                    }
                    $fields .= $this->createFields($key, $item);
                    $where .= $this->createWhere($key, $item, $group_condition);
                }
            }
        }
        return compact('fields', 'join', 'where');  
    }
}
<?php

namespace core\admin\controllers;

use core\admin\models\Model;
use core\base\controllers\BaseController;
use core\base\models\BaseModel;

class IndexController extends BaseController
{
    protected function inputData()
    {
        $db = Model::instance();

        //$res = $db->query("SELECT * FROM article1");
        $table = 'teachers';
        $color = ['red', 'blue', 'black'];

        $res = $db->get($table, [
            'fields' => ['id', 'name'],
            'where' => ['name' => 'masha, sveta', 'sername' => 'Sveta', 'fio' => 'Andrey', 'car' => 'BMW', 'color' => $color],
            'operand' => ['IN', '%LIKE%', '=', '<>', 'NOT IN'],
            'condition' => ['AND'],
            'order' => ['fio', 'name'],
            'order_direction' => ['ASC'],
            'limit' => '1',
            'join' => [
                'join_table1' => [
                    'table' => 'tech',
                    'fields' => ['id as j_id', 'name as j_name'],
                    'type' => 'left',
                    'where' => ['name' => 'sasha'],
                    'operand' => ['='],
                    'condition' => ['OR'],
                    'on' => [
                        'table' => 'teachers',
                        'fields' => ['id' , 'parent_id'],
                    ]

                    ],
                    'join_table2' => [
                        'table' => 'tech2',
                        'fields' => ['id as j2_id', 'name as j2_name'],
                        'type' => 'left',
                        'where' => ['name' => 'sasha'],
                        'operand' => ['='],
                        'condition' => ['OR'],
                        'on' => [
                            'table' => 'teachers',
                            'fields' => ['id' , 'parent_id'],
                        ]
    
                    ]
            ]
        ]);

        //exit('This is admin panel');
        exit($res);
    }
}
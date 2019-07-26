<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/16
 * Time: 10:33
 */

namespace app\manage\model;


use think\Model;

class Setting extends Model
{

    public function setting($name, $value = null)
    {
        static $config = [];
        if ($value !== null) {
            list($config, $data) = [[], ['name' => $name, 'value' => $value]];
            if (self::where('name',$name)->count() > 0) {
                return self::strict(false)->where('name',$name)->update($data) !== false;
            }
            return self::strict(false)->insert($data) !== false;
        }
        if (empty($config)) {
            $config = self::where('1=1')->column('name,value');
        }
        return isset($config[$name]) ? $config[$name] : '';
    }
}
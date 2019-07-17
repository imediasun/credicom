<?php

namespace App\modules\Core\Model;

use \App\modules\Core\Model\Base as BaseModel;

class Utils 
{
    /**
     * Sum two DateInterval objects
     * @param \DateInterval $a
     * @param \DateInterval $b
     * @return \DateInterval
     */
    static public function sumDateIntervals(\DateInterval $a, \DateInterval $b)
    {
        $result = new \DateInterval('P0D');
        foreach (str_split('ymdhis') as $prop) {
            $result->$prop = $a->$prop;
            $result->$prop += $b->$prop;
        }
        $result->i += (int)($result->s / 60);
        $result->s = $result->s % 60;
        $result->h += (int)($result->i / 60);
        $result->i = $result->i % 60;
        return $result;
    }
    
    static public function stripMagicQuotes( $string ) {
        if(get_magic_quotes_gpc()) {
            return stripslashes($string);
        }
        return $string;
    }

    static public function arrayToModel(array $data, $modelClass = BaseModel::class) {
        array_walk($data, function(&$item) use ($modelClass) {
            if(is_array($item)) $item = self::arrayToModel($item);
        });
        $result = new $modelClass($data);
        return $result;
    }

    static public function modelToArray(BaseModel $model) {
        $result = $model->toArray();
        array_walk($result, function(&$item) {
            if(is_object($item)) $item = self::modelToArray($item);
        });
        return $result;
    }

    static function merge(array $arrayA, array $arrayB)
    {
        $merged = $arrayA;
        foreach ( $arrayB as $key => $value ) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}
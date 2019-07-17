<?php
namespace App\modules\Core\Collection;

use \App\modules\Core\Model\Traits\Singleton;
use \App\modules\Core\Model\Base as BaseModel;
use PDO;
use DB;
use Config;
class Base
{
    use Singleton;

    public $table;
    public $tableAlias;
    public $primaryKey = 'id';
    public $listKey;

    public $distinct = false;

    public $calcFoundRows = false;
    public $foundRows = 0;
    public $mysqli=false;

    protected static $_fieldsCache = [];
    public $fieldMap = [];
    public $fields;
    public $fieldsDefaultValues;

    public $modelClass = BaseModel::class;

    public function __construct($data = []){
	$config_default=Config::get('database.default');
	
	if($config_default=='mysql_tunnel'){
		$host=env('TUNNELER_LOCAL_ADDRESS');
		$port=env('TUNNELER_LOCAL_PORT');
		$host=$host.':'.$port;
	}
	else{
		$host=env('DB_HOST');
	}
        $this->conn = new PDO("mysql:host=".$host.";dbname=".env('DB_DATABASE')."", env('DB_USERNAME'),  env('DB_PASSWORD', ''));
//var_dump(get_class($this) .' '. (isset($data['lang']) ? $data['lang'] : 'null') .' | '. (isset($this->lang) ? $this->lang : 'null'));
        foreach($data as $k=>$v){
            $this->{$k} = $v;
        }
        if(!$this->listKey) $this->listKey = $this->primaryKey;
        $this->initFields();
    }

    public function query($sql) {
         $arr = explode(' ',trim($sql));
        $query_operator=$arr[0]; // will print INSERT
        if($query_operator=='INSERT'){
          $sqlResult =DB::insert($sql);
        }
        else{
        $sqlResult = $this->conn->query($sql);}
        //dump('query_result',$sqlResult);
        return $sqlResult;
    }

    public function initFields()
    {
        if (isset(self::$_fieldsCache[get_class($this)]))
        {
            $this->fields = self::$_fieldsCache[get_class($this)];
            return;
        }

        $result = (count($this->fieldMap)) ? $this->fieldMap : array();
        $resultDefaultValues = [];

        $query = sprintf('SHOW COLUMNS FROM `%s`',$this->table);
        $sqlResult = $this->query($query);
  
        if($sqlResult) {
            while($row =$sqlResult->fetch(PDO::FETCH_OBJ) ) { //mysql_fetch_assoc($sqlResult)
                 if(in_array($row->Field, $result)) continue;
                $result[$row->Field] = $row->Field;
                $resultDefaultValues[$row->Field] = ($row->Null === 'NO' && $row->Default === NULL) ? '' : $row->Default;
            }
        }

        //dry run the key normalization
        $model = new $this->modelClass();
        foreach($result as $k => $v) {

            $model->normalizeKey($v);
        }

        $this->fields = $result;
        $this->fieldsDefaultValues = $resultDefaultValues;
        self::$_fieldsCache[get_class($this)] = $result;
    }

    public function normalizeQueryParams($queryParams = [])
    {
        $queryParams = array_merge(array(
            'select' => [sprintf('%s.*',($this->tableAlias) ? $this->tableAlias : $this->table)],
            'filter' =>[],
            'join' =>[],
            'group' =>[],
            'sort' =>[],
            'limit' => false,
        ),$queryParams);

        if(!is_array($queryParams['select'])) $queryParams['select'] = array($queryParams['select']);
        if(!is_array($queryParams['join'])) $queryParams['join'] = array($queryParams['join']);
        if(!is_array($queryParams['filter'])) $queryParams['filter'] = array( sprintf('%s.%s',($this->tableAlias) ? $this->tableAlias : $this->table, $this->primaryKey)=>$queryParams['filter']);
        if(!is_array($queryParams['group'])) $queryParams['group'] = array($queryParams['group']);
        if(!is_array($queryParams['sort'])) $queryParams['sort'] = array($queryParams['sort']=>'ASC');

        return $queryParams;
    }

    public function beforeQueryBuild(&$queryParams){}

    public function processQueryFilter($filter = array())
    {
        $result = array();

        $model = new $this->modelClass();

        foreach($filter as $k=>$v){

            if(is_string($k)) {
                $k = $model->denormalizeKey($k);
            }

            switch(gettype($v)) {
                case 'array' :
                    $vKeys = array_keys($v);
                    if(is_numeric(array_shift($vKeys))) {
                        $result[] = sprintf('%s IN ("%s")', $this->escapeTableKey($k),implode('","',$v));
                    } else {
                        foreach($v as $subKey => $subVal) {

                            switch($subKey) {
                                case '>' :
                                case '>=' :
                                case '<' :
                                case '<=' :
                                case '!=' :
                                    $result[] = sprintf('%s %s "%s"', $this->escapeTableKey($k), $subKey,$subVal);
                                    break;
                                case 'is' :
                                    $result[] = sprintf('%s %s %s', $this->escapeTableKey($k), $subKey,$subVal);
                                    break;
                                case 'between':
                                    $result[] = sprintf('%s BETWEEN "%s"', $this->escapeTableKey($k), implode('" AND "',$subVal));
                                    break;
                                case 'betweenRaw':
                                    $result[] = sprintf('%s BETWEEN %s', $this->escapeTableKey($k), implode(' AND ',$subVal));
                                    break;
                                case 'or' :
                                case 'and' :
                                    $or = array_map(function($item) {
                                        $where = $this->processQueryFilter($item);
                                        return sprintf('(%s)', implode(' AND ', $where));
                                    }, $subVal);

                                    $result[] = sprintf('(%s)', implode(sprintf(' %s ', strtoupper($subKey)), $or));
                                    break;
                                case 'like' :
                                    $result[] = sprintf('%s LIKE "%s"', $this->escapeTableKey($k), $subVal);
                                    break;
                                default: break;
                            }
                        }
                    }
                    break;
                default :
                    if(is_numeric($k)) {
                        if(is_array($v)) {
                            $result[] = $this->processQueryFilter($v);
                        } else {
                            $result[] = $v;
                        }
                    } else {
                        $result[] = sprintf('%s="%s"',$this->escapeTableKey($k),$v);
                    }
                    break;
            }
        }

        unset($model); //free some memory space, maybe

        return $result;
    }

    public function escapeTableKey($key) {
        if(strpos('`', $key) !== false) return $key;
        return sprintf('`%s`', implode('`.`', explode('.',$key)));
    }

    public function getQuery($queryParams = [])
    {
        $queryParams = $this->normalizeQueryParams($queryParams);

        $this->beforeQueryBuild($queryParams);

        $fields = [];
        foreach($queryParams['select'] as $k=>$v){
            $fields[] = (is_numeric($k)) ? $v : sprintf('%s as %s"',$k,$v);
        }

        $where = $this->processQueryFilter($queryParams['filter']);

        $order = [];
        foreach($queryParams['sort'] as $k=>$v){
            $order[] = sprintf('`%s` %s',implode('`.`',explode('.',$k)),$v);
        }

        $options = [];
        if($this->calcFoundRows) $options[] = 'SQL_CALC_FOUND_ROWS';
        if($this->distinct) $options[] = 'DISTINCT';

        $tableAlias = ($this->tableAlias) ? sprintf('as %s',$this->tableAlias) : '';
        $query = sprintf("SELECT %s %s FROM %s %s",implode(' ',$options), implode(', ',$fields),$this->table, $tableAlias);
        if(count($queryParams['join'])) $query = sprintf('%s %s',$query, implode(' ',$queryParams['join']));
        if(count($where)) $query = sprintf('%s WHERE %s',$query, implode(' AND ',$where));
        if(count($queryParams['group'])) $query = sprintf('%s GROUP BY %s',$query, implode(',',$queryParams['group']));
        if(count($order)) $query = sprintf('%s ORDER BY %s',$query, implode(',',$order));
        if($queryParams['limit']) $query = sprintf('%s LIMIT %s',$query, $queryParams['limit']);
//var_dump($query);
        return $query;
    }

	   public function getListIn($query)
    {
		$modelClass = $this->modelClass;
        $sqlResult = $this->query($query);
        if($sqlResult) {
            while($row = $sqlResult->fetch(PDO::FETCH_OBJ)) {

			foreach($row as $key=>$value){
                    $row2[$key]=$value;
                }
                $result[$row2[$this->listKey]] = new $modelClass($row);


            }

        }

        if($this->calcFoundRows) {
            $this->foundRows = (int) array_shift(mysql_fetch_row($this->query('SELECT FOUND_ROWS()')));
        }

        return $result;
    }
	
    public function getList($queryParams = [])
    {
        $result = new \App\modules\Core\Model\Collection();
		
        $modelClass = $this->modelClass;
         $query = $this->getQuery($queryParams);
        $sqlResult = $this->query($query);
        if($sqlResult) {
            while($row = $sqlResult->fetch(PDO::FETCH_OBJ)) {//mysql_fetch_assoc($sqlResult)

			foreach($row as $key=>$value){
                    $row2[$key]=$value;
                }
                $result[$row2[$this->listKey]] = new $modelClass($row);


            }

        }

        if($this->calcFoundRows) {
            $this->foundRows = (int) array_shift(mysql_fetch_row($this->query('SELECT FOUND_ROWS()')));
        }

        return $result;
    }

    public function load($id)
    {
        $result = null;

        $queryParams = $id;
        if(!is_array($id)){
            $queryParams = array('filter' => $id);
        }
        $queryParams['limit'] = 1;
        $list = $this->getList($queryParams);
        if(count($list)){
            $result = $list->getIterator()->current();
        }

        return $result;
    }

    public function emptyLoad($data = array()) {
        foreach($this->fieldsDefaultValues  as $fieldAlias => $fieldDefaultValue) {
            $result[$fieldAlias] = (isset($data[$fieldAlias])) ? $data[$fieldAlias] : $fieldDefaultValue;
        }
        
        return new $this->modelClass($result);
    }

    /* save */
    public function saveQuery($data = array())
    {
        $insertSet = array();
        $updateSet = array();
        //create insert and update sets with table fields only
        foreach($data as $k => $v)
        {
            $field = (isset($this->fields[$k])) ? $this->fields[$k] : $k;
            if($data instanceof BaseModel) {
                $field = $data->denormalizeKey($field);
            }
            if(!in_array($field, $this->fields)) continue;

            if(is_null($v)) {
                $queryPart = sprintf('`%s` = NULL', $field);
            } else {
                $queryPart = sprintf('`%s` = "%s"', $field, ($v));//mysql_real_escape_string
            }
            $insertSet[] = $queryPart;
            if($k != $this->primaryKey) $updateSet[] = $queryPart;
        }

        $sql = sprintf('INSERT INTO %s SET %s ON DUPLICATE KEY UPDATE %s',
            $this->table,
            implode(', ', $insertSet),
            implode(', ', $updateSet)
        );

        return $sql;
    }

    public function escape($value)
    {
        $value = mysql_real_escape_string($value);

        return $value;
    }

    public function _save(BaseModel $data)
    {
        $saveQuery = $this->saveQuery($data);
//var_dump($saveQuery);
        $saved = $this->query($saveQuery);

        if($saved && !$data->get($this->primaryKey)) {
            $id = DB::getPdo()->lastInsertId();//$id = mysql_insert_id();
            if($id) $data->set($this->primaryKey, $id);
        }

        return $data;
    }


/*  public function save(BaseModel $data)
    {
        $saveQuery = $this->saveQuery($data);
        $saved = $this->query($saveQuery);
        //dump('$saved',$saved);
        if($saved && !$data->get($this->primaryKey)) {
            $id = $this->conn->lastInsertId() ;//mysql_insert_id();

            if($id) $data->set($this->primaryKey, $id);
        }


        return $data;
    }*/

    /* DELETE */
    public function deleteQuery($data = [])
    {
        $where = [];
        foreach($data as $k => $v){
            switch(gettype($v)) {
                case 'array' :
                    $where[] = sprintf('`%s` IN ("%s")', $k, implode('","',$v));
                    break;
                default:
                    $where[] = sprintf('`%s` = "%s"', $k, $v);
                    break;
            }
        }

        $query = sprintf('DELETE FROM %s WHERE %s', $this->table, implode(' AND ', $where));

        return $query;
    }

    public function beforeDelete($data = []) {}
    public function afterDelete($data = []) {}

    public function delete($data = [])
    {
        if(!is_array($data)) $data = array($this->primaryKey => $data);

        $this->beforeDelete($data);

        $deleteQery = $this->deleteQuery($data);
//var_dump($deleteQery);
        $result = $this->query($deleteQery);

        $this->afterDelete($data);
        return $result;
    }
}
<?php
namespace core\base\model;
use \core\base\exceptions\DbException;
class BaseModel extends BaseModelMethods{
    use \core\base\controller\Singleton;

    protected $db;

    private function __construct()
    {
        $this->db = @new \mysqli(HOST, USER, PASS, DB_NAME);
        if($this->db->connect_error){
            throw new DbException('Ошибка подключения к базе данных '.$this->db->connect_errno.' '.$this->db->connect_error);
        } 
        $this->db->query("SET NAMES UTF8");
    }

    /**
     * $crud = r - select c - insert u - update d - delete
     */
    final public function query($query, $crud = 'r', $returnId= false)
    {
        $result = $this->db->query($query);
        if($this->db->affected_rows===-1)
        {
            throw new DbException('Ошибка в SQL запросе: '.$query.' - '.$this->db->errno.' '.$this->db->error);
        }

        switch ($crud) {
            case 'r':
                if($result->num_rows)
                {
                    $res = [];
                    for ($i=0; $i < $result->num_rows; $i++) { 
                        $res[] = $result->fetch_assoc();
                    }
                    return $res;
                }
                return false;
            break;
            case 'c':
                if($returnId)
                {
                    return $this->db->insert_id;
                }
                return true;
            break;
            default:
                return true;
                break;
        }
    }
    /**
     *  $table - имя таблицы
     *  array $set
     * 'fields'=>['id', 'name'],
     * 'where' => ['surname'=>'Smirnova', 'name'=>'Masha', 'middleName' => 'Sergeevna'],
     * 'operand'=> ['=','<>'],
     * 'condition'=> ['AND'],
     * 'order'=> ['surname','name'],
     * 'orderDirection'=> ['ASC','DESC'],
     * 'limit'=> '1'
     * 'join' => [
     *               [
     *                  'table' => 'joinTable1',
     *                   'fields' => ['id as jId', 'name as jName'],
     *                   'type' => 'left',
     *                   'where' => ['name' => 'sasha'],
     *                   'operand' => ['='],
     *                   'condition' => ['OR'],
     *                  'on' => ['id', 'parentId'],
     *                  'groupCondition'=>'AND'
     *              ],
     *               [
     *                   'table' => 'joinTable2',
     *                   'fields' => ['id as j2Id', 'name as j2Name'],
     *                   'type' => 'left',
     *                   'where' => ['name' => 'sasha'],
     *                   'operand' => ['='],
     *                   'condition' => ['OR'],
     *                  'on' => [
     *                      'table' => 'teachers',
     *                      'fileds' => ['id', 'parentId']
     *                  ]
     *              ]
     *          ]
     */
    final public function get($table, $set = [])
    {
        $fields = $this->createFields($set, $table);
        $where = $this->createWhere($set, $table);

        if(!$where)
        {
            $newWhere = true;
        }
        else {
            $newWhere = false;
        }

        $joinArr = $this->createJoin($set, $table, $newWhere);

        $fields.=$joinArr['fields'];
        $join=$joinArr['join'];
        $where.=$joinArr['where'];

        $fields = rtrim($fields, ',');

        $order = $this->createOrder($set, $table);
        
        $limit = $set['limit']?'LIMIT '.$set['limit']:'';

        $query = "SELECT $fields FROM $table $join $where $order $limit";
        return $this->query($query);
    }

    /**
     * $table - таблица вставки
     * $set - массив параметров
     * $fielfd = [поле=>значение]
     * files - массив с файлами
     * except - ['искл1', 'искл2'] исключать из массива для добавления в запрос
     * returnId - есть или нет идентификатор
     * return mixed
     */
    final public function add($table, $set = [])
    {
       
        $set['fields'] = (is_array($set['fields'])&&!empty($set['fields'])) ? $set['fields']:$_POST;
        $set['files'] = (is_array($set['files'])&&!empty($set['files'])) ? $set['files']:false;

        if(!$set['fields'] && !$set['files'])
            return false;

        $set['returnId'] = $set['returnId']? $set['returnId']:false;
        $set['except'] = (is_array($set['except'])&&!empty($set['except']))?$set['except']:false;

        $insertArr = $this->createInsert($set['fields'], $set['files'], $set['except']);
        if($insertArr)
        {
        $query = "INSERT INTO $table ({$insertArr['fields']}) VALUES ({$insertArr['values']})";

            return $this->query($query, 'c', $set['returnId']);
        }

        return false;
    }

    final public function edit($table, $set = [])
    {
        $set['fields'] = (is_array($set['fields'])&&!empty($set['fields'])) ? $set['fields']:$_POST;
        $set['files'] = (is_array($set['files'])&&!empty($set['files'])) ? $set['files']:false;

        if(!$set['fields'] && !$set['files'])
            return false;

        $set['except'] = (is_array($set['except'])&&!empty($set['except']))?$set['except']:false;

        if(!$set['allRows']) {
            if ($set['where']) {
                $where = $this->createWhere($set);
            } else {
                $columns = $this->showColumns($table);

                if (!$columns) {
                    return false;
                }

                if ($columns['idRow'] && $set['fields'][$columns['idRow']]) {
                   $where = 'WHERE ' . $columns['idRow']. '=' .$set['fields'][$columns['idRow']];
                   unset($set['fields'][$columns['idRow']]);
                }
            }
        }
        $update = $this->createUpdate($set['fields'], $set['files'], $set['except']);

        $query = "UPDATE $table SET $update $where";

        return $this->query($query, 'u');
    }
    
    /**
     *  $table - имя таблицы
     *  array $set
     * 'fields'=>['id', 'name'],
     * 'where' => ['surname'=>'Smirnova', 'name'=>'Masha', 'middleName' => 'Sergeevna'],
     * 'operand'=> ['=','<>'],
     * 'condition'=> ['AND'],
     * 'join' => [
     *               [
     *                  'table' => 'joinTable1',
     *                   'fields' => ['id as jId', 'name as jName'],
     *                   'type' => 'left',
     *                   'where' => ['name' => 'sasha'],
     *                   'operand' => ['='],
     *                   'condition' => ['OR'],
     *                  'on' => ['id', 'parentId'],
     *                  'groupCondition'=>'AND'
     *              ],
     *               [
     *                   'table' => 'joinTable2',
     *                   'fields' => ['id as j2Id', 'name as j2Name'],
     *                   'type' => 'left',
     *                   'where' => ['name' => 'sasha'],
     *                   'operand' => ['='],
     *                   'condition' => ['OR'],
     *                  'on' => [
     *                      'table' => 'teachers',
     *                      'fileds' => ['id', 'parentId']
     *                  ]
     *              ]
     *          ]
     */
    public function delete($table, $set)
    {
        $table = trim($table);
        
        $where = $this->createWhere($set, $table);

        $columns = $this->showColumns($table);
        if(!$columns) return false;

        if(is_array($set['fields']) && !empty($set['fields']))
        {
            if($columns['idRow'])
            {
                $key = array_search($columns['idRow'], $set['fields']);
                if($key !== false)
                {
                    unset($set['fields'][$key]);
                }
            }
            
            $fields = [];
            foreach ($set['fields'] as $field) {
                $fields[$field] = $columns[$field]['Default'];
            }
            $update = $this->createUpdate($fields, false, false);
            $query = "UPDATE $table SET $update $where";
           
        }
        else {
            $joinArr = $this->createJoin($set, $table);
            $join = $joinArr['join'];
            $joinTables = $joinArr['tables'];

            $query = 'DELETE '.$table.$joinTables.' FROM '.$table.' '.$join. ' '.$where;
        }
        var_dump($query);
        return $this->query($query, 'u');
    }

    final public function showColumns($table)
    {
        $query = "SHOW COLUMNS FROM $table";

        $res = $this->query($query);
        
        $columns = [];
        
        if($res)
        {
            foreach ($res as $col) {
                //var_dump($col);
                $columns[$col['Field']] = $col;
                if ($col['Key'] === 'PRI') {
                    $columns['idRow'] = $col['Field'];
                }
            }
        }
        return  $columns;
    }
}
?>
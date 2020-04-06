<?php
namespace core\base\model;
use \core\base\exceptions\DbException;
class BaseModel{
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
     */
    final function get($table, $set = [])
    {
        
        $fields = $this->createFields($table, $set);
        $where = $this->createWhere($table, $set);
        $joinArr = $this->createJoin($table, $set);

        $fields.=$joinArr['fields'];
        $join=$joinArr['join'];
        $where.=$joinArr['where'];

        $fields = rtrim($fields, ',');

        $order = $this->createOrder($table, $set);
        
        $limit = $set['limit']?$set['limit']:'';

        $query = "SELECT $fields FROM $table $join $where $order $limit";
        
        return $this->query($query);
    }

    protected function createFields($table = false, $set)
    {
        $set['fields'] = (is_array($set['fields'])&&!empty($set['fields']))?$set['fields']:['*'];

        $table = $table ? $table.'.':'';

        $fields = '';

        foreach ( $set['fields'] as $field) {
            $fields.=$table.$field.',';
        }
        return $fields;
    }

    protected function createWhere($table, $set, $instruction = 'WHERE')
    {
        $table = $table ? $table.'.':'';

        $where = '';

        if(is_array($set['where'])&&!empty($set['where']))
        {
            $set['operand'] = (is_array($set['operand'])&&!empty($set['operand']))?$set['operand']:['='];
            $set['condition'] = (is_array($set['condition'])&&!empty($set['condition']))?$set['condition']:['AND'];

            $where = $instruction;

            $o_count = 0;
            $c_count = 0;

            foreach ($set['where'] as $key => $item) {
                $where.=' ';

                if($set['operand'][$o_count]){
                    $operand = $set['operand'][$o_count];
                    $o_count++;
                }
                else {
                    $operand = $set['operand'][$o_count-1];
                }

                if($set['condition'][$o_count]){
                    $condition = $set['condition'][$c_count];
                    $c_count++;
                }
                else {
                    $condition = $set['condition'][$c_count-1];
                }

                if($operand ==='IN'||$operand==='NOT IN')
                {
                    if(is_string($item)&&strpos($item,'SELECT'))
                    {
                        $in_str = $item;
                    }
                    else {
                        if(is_array($item)) 
                        {
                            $tempItem = $item;
                        }
                        else {
                            $tempItem = explode(',', $item);
                        }

                        $inStr = '';
                        foreach ($tempItem as $v) {
                            $inStr.="'".trim($v)."',"; 
                        }
                    }
                    $where.=$table.$key.' '.$operand.' ('.trim($inStr,',').') '.$condition;
                    var_dump($where);
                }
            }
        }
    }

    protected function createJoin($table, $set)
    {
        
    }

    protected function createOrder($table, $set)
    {
        $table = $table ? $table.'.':'';

        $orderBy ='ORDER BY ';

        if(is_array($set['order'])&&!empty($set['order']))
        {
            $set['orderDirection'] = (is_array($set['orderDirection'])&&!empty($set['orderDirection']))?$set['orderDirection']:['ASC'];

            $directCount=0;
            foreach ($set['order'] as $order) {
                if ($set['orderDirection'][$directCount]) {
                    $orderDirection = strtoupper($set['orderDirection'][$directCount]);
                    $directCount++;
                }
                else {
                    $orderDirection = strtoupper($set['orderDirection'][$directCount-1]);
                }
                $orderBy.=$table.$order.' '.$orderDirection.',';
            }
            $orderBy = rtrim($orderBy,',');
        }
        return $orderBy;
    }
}
?>
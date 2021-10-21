<?php
/**
 * @author emrdev
 * @author emrdev.ru <emrdev@outlook.com>
 */
class Repository
{
    /** @var PDO $pdo */
    private $pdo;

    private $manager;

    private $fetch_mode = PDO::FETCH_ASSOC;

    private $primary;

    public function __construct(){
        $this->connection();

    }

    /**
     * @param mixed $primary
     */
    public function setPrimary($primary)
    {
        $this->primary = $primary;
        return $this;
    }
     



    /**
     * Entity name
     * @return Repository
     */
    public function getManager(string $entity): Repository
    {
        $this->manager = $entity;
        $this->initPrimary();
        return $this;
    }

    public function findAll(){
        return $this->query("SELECT * FROM {$this->manager}");
    }

    /**
     * Find by id
     * @param int $id
     */
    public function find(int $id){
        return $this->single_query("SELECT * FROM {$this->manager} WHERE {$this->primary} = :id",['id'=>$id]);
    }

    public function findBy(array $parameters = [],$start = null, $limit = null){
        $sql = "SELECT * FROM {$this->manager} WHERE ";
        $values = '';
        foreach ($parameters as $key => $param){
            if($values){
                $values .= 'AND ';
            }
            $values .="{$key} = :$key ";
        }
        $sql.=$values;

        if($limit){
            $sql .= " LIMIT :limit ";
            $parameters['limit'] = $limit;
        }
        if($start){
            $sql .= " OFFSET :start ";
            $parameters['start'] = $start;
        }  
        return $this->query($sql,$parameters);
    }

    public function findOneBy(array $parameters = [],$start = null, $limit = null){
        $sql = "SELECT * FROM {$this->manager} WHERE ";
        $values = '';
        foreach ($parameters as $key => $param){
            if($values){
                $values .= 'AND ';
            }
            $values .="{$key} = :$key ";
        }
        $sql .=$values;
        if($start){
            $sql .= "OFFSET :offset ";
            $parameters['offset'] = $start;
        }
        if($limit){
            $sql .= "LIMIT :limit ";
            $parameters['limit'] = $limit;
        }
        return $this->single_query($sql,$parameters);
    }

    public function insert(array $parameters = []){
        $sql = "INSERT INTO {$this->manager} SET ";
        $values = '';
        foreach ($parameters as $key => $param){
            if($values){
                $values .=', ';
            }
            $values .="{$key} = :$key ";
        }
        $sql .= $values;

        return $this->single_query($sql,$parameters);
    }

    public function update(array $parameters = [],$id){
        $sql = "UPDATE  {$this->manager} SET ";
        $values = '';
        foreach ($parameters as $key => $param){
            if($values){
                $values .=', ';
            }
            $values .="{$key} = :$key ";
        }
        $sql .= $values;
        $sql .= "  WHERE {$this->primary} = :id";
        $parameters['id'] = $id;
        return $this->single_query($sql,$parameters);
    }
    public function remove($id){
        $sql = "DELETE  FROM {$this->manager}  WHERE {$this->primary} = :id";
        $parameters['id'] = $id; 
        return $this->single_query($sql,$parameters);
    }

    public function removeAll(){
        $sql = "DELETE  FROM {$this->manager}";
        return $this->single_query($sql);
    }

    public function query(string $sql, array $parameters = []){
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);
        return $stmt->fetchAll($this->fetch_mode);
    }

    public function single_query(string $sql, array $parameters = []){
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);

        return $stmt->fetch($this->fetch_mode);
    }

    public function initPrimary(){
        $query = $this->single_query( "show index from {$this->manager} where Key_name = 'PRIMARY'");
        if(isset($query['Column_name'])){
            $this->primary = $query['Column_name'];
        }
        return $this->primary;
    }

    private function connection(){
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO("mysql:host=".DB_HOSTNAME.";dbname=".DB_DATABASE.";charset=utf8",DB_USERNAME, DB_PASSWORD,$options);
    }
}

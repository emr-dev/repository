<?php



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
        foreach ($parameters as $key => $param){
            $sql .="{$key} = :$key ";
        }
        if($start){
            $sql .= "OFFSET :$start ";
        }
        if($limit){
            $sql .= "LIMIT :$limit ";
        }
        return $this->query($sql,$parameters);
    }

    public function findOneBy(array $parameters = [],$start = null, $limit = null){
        $sql = "SELECT * FROM {$this->manager} WHERE ";
        foreach ($parameters as $key => $param){
            $sql .="{$key} = :$key ";
        }
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

    public function insert(){}

    public function update(){}

    public function remove(){}

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
        $this->pdo = new PDO(REPOSITORY_PDO.":host=".REPOSITORY_HOST.";dbname=".REPOSITORY_DBNAME.";charset=".REPOSITORY_CHARSET,REPOSITORY_USER, REPOSITORY_PASSWORD,$options);
    }
}
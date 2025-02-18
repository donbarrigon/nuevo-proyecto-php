<?php
namespace App\Orm;

class Model 
{
    /**
     * @var Conexion
     */
    protected Conexion $conexion;

    /**
     * es el nombre de la tabla o colecion en la base de datos
     * @var string
     */
    protected string $modelName;

    /**
     * @var array<array{type: string, length: int}>
     */
    protected array $modelStruct; 

    /**
     * @var array<string>
     */
    protected array $fillable;

    /**
     * @var array<string>
     */
    protected array $guarded;

    /**
     * @var array<string>
     */
    protected array $fields; // se usa para crear los querys
    protected array $where;  // se usa para crear los querys
    protected array $order;  // se usa para crear los querys
    protected int   $limit;  // se usa para crear los querys
    protected int   $offset; // se usa para crear los querys
    protected array $group;  // se usa para crear los querys

    protected $data; // almacena los resultados de los querys de momento

    public function __construct(Conexion $conexion)
    { 
        // se hace asi por si los herederos sobreescriben este metodo
        $this->conexion = $conexion;
    }

    public function beforeSave():   ?string { return null; }
    public function afterSave():    ?string { return null; }
    public function beforeDelete(): ?string { return null; }
    public function afterDelete():  ?string { return null; }
    public function beforeCreate(): ?string { return null; }
    public function afterCreate():  ?string { return null; }
    public function beforeUpdate(): ?string { return null; }
    public function afterUpdate():  ?string { return null; }

    /**
     * @param int|string|array<int|string> $key
     */
    public function findById(int|string $key, array $fields = []): ?string
    {
        
        $this->fields = $this->getSelectFields($fields);

        if(count($this->fields) === 0)
        {
            return "No hay atributos válidos para buscar en [" . implode(', ', $fields) . "]";
        }

        // retorna null si todo va bien
        return match($this->conexion->driver) {
            'mongodb' =>    $this->findByIdMongodb($key),
            'mysql' =>      $this->findByIdMysql($key),
            'postgresql' => $this->findByIdPostgresql($key),
             default =>     "Unsupported driver: {$this->conexion->driver}"
        };
    }

    private function findByIdMongodb(int|string|array $key): ?string
    {
        try {
            // MongoDB utiliza las claves de la proyección como un array
            $selectFields = array_fill_keys($this->fields, 1);
            $collection = $this->conexion->db->selectCollection($this->modelName);

            if (is_array($key))
            {
                $key = array_map(fn($k) => is_string($k) && preg_match('/^[0-9a-fA-F]{24}$/', $k) ? new \MongoDB\BSON\ObjectId($k) : $k, $key);
                $filter = ['_id' => ['$in' => $key]];
            } else {
                $key = is_string($key) && preg_match('/^[0-9a-fA-F]{24}$/', $key) ? new \MongoDB\BSON\ObjectId($key) : $key;
                $filter = ['_id' => $key];
            }

            $query = $collection->find($filter, ['projection' => $selectFields]);
            $this->data = iterator_to_array($query);
            
            return null; // Todo bien
        } catch (\Exception $e) {
            return "Error en findMongodb: " . $e->getMessage();
        }
    }

    private function findByIdMysql(int|string|array $key): ?string
    {
        try {
            // Escapa los atributos para asegurarse de que no haya inyecciones
            $selectFields = implode(', ', array_map(function ($field) {
                return "`" . mysqli_real_escape_string($this->conexion->db, $field) . "`"; 
            }, $this->fields));

            if (is_array($key)) 
            {
                $placeholders = implode(',', array_fill(0, count($key), '?'));
                $query = "SELECT $selectFields FROM $this->modelName WHERE id IN ($placeholders)";
            } else {
                $query = "SELECT $selectFields FROM $this->modelName WHERE id = ?";
            }

            // Preparar la consulta
            $stmt = $this->conexion->db->prepare($query);
            
            if (!$stmt) {
                return "Error preparando consulta MySQL: " . $this->conexion->db->error;
            }

            if (is_array($key)) {
                $types = str_repeat('s', count($key)); // 's' es para string, 'i' es para enteros
                $stmt->bind_param($types, ...$key);
            } else {
                $type = is_int($key) ? 'i' : 's';
                $stmt->bind_param($type, $key);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$result) {
                return "Error ejecutando consulta MySQL: " . $stmt->error;
            }

            $this->data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            return null; // Todo bien
        } catch (\Exception $e) {
            return "Error en findMysql: " . $e->getMessage();
        }
    }

    private function findByIdPostgresql(int|string|array $key): ?string
    {
        try {
            // Escapar los nombres de los campos
            $selectFields = implode(', ', array_map(function ($field) {
                return '"' . preg_replace('/[^a-zA-Z0-9_]/', '', $field) . '"';
            }, $this->fields));

            if (is_array($key)) {
                // Se usa el operador IN para consultas con múltiples claves
                $placeholders = implode(',', array_map(fn($i) => '$' . ($i + 1), range(0, count($key) - 1)));
                $query = "SELECT $selectFields FROM \"$this->modelName\" WHERE id IN ($placeholders)";
                $stmtName = "find_" . md5($query);
                
                $stmt = @pg_prepare($this->conexion->db, $stmtName, $query);
                if (!$stmt) {
                    return "Error preparando consulta PostgreSQL: " . pg_last_error($this->conexion->db);
                }
                
                $result = pg_execute($this->conexion->db, $stmtName, $key);
            } else {
                $query = "SELECT $selectFields FROM \"$this->modelName\" WHERE id = $1";
                $stmtName = "find_single_" . md5($query);
                
                $stmt = @pg_prepare($this->conexion->db, $stmtName, $query);
                if (!$stmt) {
                    return "Error preparando consulta PostgreSQL: " . pg_last_error($this->conexion->db);
                }
                
                $result = pg_execute($this->conexion->db, $stmtName, [$key]);
            }

            if (!$result) {
                return "Error ejecutando consulta PostgreSQL: " . pg_last_error($this->conexion->db);
            }

            // Recuperar los resultados
            $this->data = pg_fetch_all($result);
            
            return null; // Todo bien
        } catch (\Exception $e) {
            return "Error en findPostgresql: " . $e->getMessage();
        }
    }

    private function getSelectFields(array $fields): array
    {
        if (count($fields) === 0) {
            return $this->getAllFieldsNames();
        }

        if (count($this->fields) === 0)
        {
            return $fields;
        }
    
        $result = [];
        foreach ($fields as $field) {
            if ($this->hasField($field)) {
                $result[] = $field;
            }
        }

        return $result;
    }

    public function hasField(string $field): bool
    {
        if (isset($this->modelStruct[$field]))
        {
            return true;
        }
        return false;
    }

    public function getAllFieldsNames(): array
    {
        return array_keys($this->modelStruct);
    }
}
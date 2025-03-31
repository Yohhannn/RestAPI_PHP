<?php
require_once "config/Database.php";
require_once "models/Department.php";
require_once "contracts/IBaseRepository.php";

class DepartmentRepository implements IBaseRepository {
    protected $databaseContext;
    protected $table;

    public function __construct($databaseContext, $table) {
        $this->databaseContext = $databaseContext;
        $this->table = $table;
    }

    public function GetAllList() : array {
        $query = "SELECT * FROM {$this->table}";
        $result = $this->ExecuteSqlQuery($query, []);

        return $this->BuildResultList($result);
    }

    public function GetById(int $id) : ?object {
        $query = "SELECT * FROM {$this->table} WHERE ID = :id";
        $result = $this->ExecuteSqlQuery($query, [':id' => $id]);

        return $this->BuildResult($result);
    }

    public function Add($entity) {
        if (!isset($entity['Name']) || empty(trim($entity['Name']))) {
            throw new Exception("Department Name is required");
        }
    
        $department = new Department();
        $department->Name = $entity['Name']; // Correct access to associative array
    
        $query = "INSERT INTO {$this->table} (Name) VALUES (:name)";
        $params = [':name' => $department->Name];
    
        $this->ExecuteSqlQuery($query, $params);
    
        return $department;
    }

    public function Update($entity) : void {
        if (!isset($entity['Name']) || empty(trim($entity['Name']))) {
            throw new Exception("Department Name is required");
        }
    
        if (!isset($entity['Id']) || !is_numeric($entity['Id'])) {
            throw new Exception("Department ID is required and must be a number");
        }
    
        $department = new Department();
        $department->Name = $entity['Name'];
        $department->Id = $entity['Id'];
    
        $query = "UPDATE {$this->table} SET Name = :name WHERE ID = :id";
        $params = [
            ':name' => $department->Name,
            ':id'   => $department->Id
        ];
    
        $this->ExecuteSqlQuery($query, $params);
    }    

    public function delete(int $id) : void {
        $query = "DELETE FROM {$this->table} WHERE ID = :id";
        $params = [':id' => $id];
        $this->ExecuteSqlQuery($query, $params);
    }

    private function ExecuteSqlQuery(string $query, array $params) {
        $statementObject = $this->databaseContext->prepare($query);
        $statementObject->execute($params);

        if (stripos($query, "SELECT") === 0) {
            return $statementObject->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    private function BuildResult(?array $result) : ?Department {
        if (!$result || empty($result[0])) {
            return null;
        }

        $row = $result[0];

        $department = new Department();
        $department->Id = $row['Id'];
        $department->Name = $row['Name'];

        return $department;
    }

    private function BuildResultList(array $result) : array {
        $departments = [];

        foreach ($result as $row) {
            $department = new Department();
            $department->Id = $row['Id'];
            $department->Name = $row['Name'];
            $departments[] = $department;
        }

        return $departments;
    }
}

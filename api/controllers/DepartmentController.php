<?php
require_once "repositories\DepartmentRepository.php";
require_once "config\Database.php"; // Ensure Database class is included

class DepartmentController {
    private DepartmentRepository $departmentRepository;

    public function __construct() {
        $database = new Database();
        $this->databaseConnection = $database->getConnection();
        $this->departmentRepository = new DepartmentRepository($this->databaseConnection, "Department");
    }

    public function GetAllDepartment(): void {
        echo json_encode($this->departmentRepository->GetAllList());
    }

    public function GetDepartmentById(int $id): void {
        echo json_encode($this->departmentRepository->GetById($id));
    }

    public function AddDepartment($department) {
        $this->departmentRepository->Add($department);
        echo "Data Added Successfully";
    }

    public function UpdateDepartment($entity) {
        $this->departmentRepository->update($entity);
        echo "Data Updated Successfuly";
    }

    public function DeleteDepartment(int $id) {
        $this->departmentRepository->delete($id);
        echo "Data Deleted Successfully";
    }
}
<?php
declare(strict_types=1);

class VehicleAllocation {
    private PDO $db;
    private string $selected_date;
    private string $message = '';
    private string $error = '';

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->selected_date = $_POST['allocation_date'] ?? date('Y-m-d');
    }

    public function getPresentEmployees(): array {
        $query = "SELECT e.id, e.name 
                  FROM employees e 
                  INNER JOIN attendance a ON e.id = a.employee_id 
                  WHERE a.date = :selected_date AND a.status IN ('P', 'J')
                  ORDER BY e.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':selected_date', $this->selected_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicles(): array {
        $query = "SELECT v.id, v.name, v.vehicle_number, 
                         CASE WHEN va.id IS NOT NULL THEN 1 ELSE 0 END AS is_allocated
                  FROM vehicles v
                  LEFT JOIN vehicle_allocations va 
                  ON v.id = va.vehicle_id AND va.allocation_date = :selected_date
                  ORDER BY v.vehicle_number, v.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':selected_date', $this->selected_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllocatedVehicles(): array {
        $query = "SELECT va.id, v.vehicle_number, v.name AS vehicle_name, e.name AS employee_name
                  FROM vehicle_allocations va
                  INNER JOIN vehicles v ON va.vehicle_id = v.id
                  INNER JOIN employees e ON va.employee_id = e.id
                  WHERE va.allocation_date = :selected_date
                  ORDER BY v.vehicle_number, v.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':selected_date', $this->selected_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allocateVehicle(string $employee_id, string $vehicle_id): void {
        try {
            if (empty($employee_id) || empty($vehicle_id)) {
                throw new Exception('All fields are required.');
            }

            $this->db->beginTransaction();

            // Check if vehicle is already allocated
            $check_query = "SELECT id FROM vehicle_allocations 
                           WHERE vehicle_id = :vehicle_id AND allocation_date = :selected_date";
            $check_stmt = $this->db->prepare($check_query);
            $check_stmt->bindParam(':vehicle_id', $vehicle_id);
            $check_stmt->bindParam(':selected_date', $this->selected_date);
            $check_stmt->execute();

            if ($check_stmt->rowCount() > 0) {
                throw new Exception('Vehicle is already allocated for the selected date.');
            }

            // Allocate vehicle
            $query = "INSERT INTO vehicle_allocations (employee_id, vehicle_id, allocation_date) 
                      VALUES (:employee_id, :vehicle_id, :selected_date)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':vehicle_id', $vehicle_id);
            $stmt->bindParam(':selected_date', $this->selected_date);

            if (!$stmt->execute()) {
                throw new Exception('Failed to allocate vehicle.');
            }

            $this->db->commit();
            $this->message = 'Vehicle allocated successfully!';
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->error = $e->getMessage();
        }
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getError(): string {
        return $this->error;
    }

    public function getSelectedDate(): string {
        return $this->selected_date;
    }
}
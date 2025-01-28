<?php
class Prescription {
    private $conn;
    private $table_name = 'prescriptions';

    public $prescription_id;
    public $customer_id;
    public $doctor_name;
    public $prescription_date;
    public $notes;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new Prescription
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET customer_id = :customer_id, doctor_name = :doctor_name, prescription_date = :prescription_date, notes = :notes";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
        $this->doctor_name = htmlspecialchars(strip_tags($this->doctor_name));
        $this->prescription_date = htmlspecialchars(strip_tags($this->prescription_date));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        // Bind
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':doctor_name', $this->doctor_name);
        $stmt->bindParam(':prescription_date', $this->prescription_date);
        $stmt->bindParam(':notes', $this->notes);

        if ($stmt->execute()) {
            $this->prescription_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read Prescription by ID
    public function readById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':prescription_id', $this->prescription_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Read All Prescriptions by Customer ID
    public function readByCustomerId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update Prescription
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET doctor_name = :doctor_name, prescription_date = :prescription_date, notes = :notes 
                  WHERE prescription_id = :prescription_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->doctor_name = htmlspecialchars(strip_tags($this->doctor_name));
        $this->prescription_date = htmlspecialchars(strip_tags($this->prescription_date));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->prescription_id = htmlspecialchars(strip_tags($this->prescription_id));

        // Bind
        $stmt->bindParam(':doctor_name', $this->doctor_name);
        $stmt->bindParam(':prescription_date', $this->prescription_date);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':prescription_id', $this->prescription_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Prescription
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE prescription_id = :prescription_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':prescription_id', $this->prescription_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Static: Get Prescriptions Count by Customer ID
    public static function getCountByCustomerId($db, $customer_id) {
        $query = "SELECT COUNT(*) AS count FROM prescriptions WHERE customer_id = :customer_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    }

    // Static: Get All Prescriptions
    public static function getAll($db) {
        $query = "SELECT * FROM prescriptions";
        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters
    public function getPrescriptionId() {
        return $this->prescription_id;
    }

    public function getCustomerId() {
        return $this->customer_id;
    }

    public function getDoctorName() {
        return $this->doctor_name;
    }

    public function getPrescriptionDate() {
        return $this->prescription_date;
    }

    public function getNotes() {
        return $this->notes;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setPrescriptionId($prescription_id) {
        $this->prescription_id = $prescription_id;
    }

    public function setCustomerId($customer_id) {
        $this->customer_id = $customer_id;
    }

    public function setDoctorName($doctor_name) {
        $this->doctor_name = $doctor_name;
    }

    public function setPrescriptionDate($prescription_date) {
        $this->prescription_date = $prescription_date;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
    }
}
?>

<?php
class User {
    private $conn;
    private $table_name = 'users';

    public $user_id;
    public $role;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $phone_number;
    public $address;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create User
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET role=:role, first_name=:first_name, last_name=:last_name, email=:email, password=:password, phone_number=:phone_number, address=:address";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Bind
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':address', $this->address);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read User by ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update User
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number, address = :address WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':user_id', $this->user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete User
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

?
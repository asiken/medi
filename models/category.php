<?php
class Category {
    private $conn;
    private $table_name = 'categories';

    public $category_id;
    public $name;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create Category
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));

        // Bind
        $stmt->bindParam(':name', $this->name);

        if ($stmt->execute()) {
            $this->category_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read Category by ID
    public function readById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update Category
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name WHERE category_id=:category_id";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        // Bind
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':category_id', $this->category_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Category
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE category_id=:category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $this->category_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get All Categories
    public static function getAllCategories($db) {
        $query = "SELECT * FROM categories";
        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getter for Category ID
    public function getCategoryId() {
        return $this->category_id;
    }

    // Getter for Name
    public function getName() {
        return $this->name;
    }

    // Getter for Created At
    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setter for Category ID
    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }

    // Setter for Name
    public function setName($name) {
        $this->name = $name;
    }
}
?>

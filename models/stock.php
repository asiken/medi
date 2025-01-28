<?php
class Stock {
    private $conn;
    private $table_name = 'stock';

    public $stock_id;
    public $product_id;
    public $quantity;
    public $unit_price;
    public $expiry_date;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new stock entry
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET product_id = :product_id, quantity = :quantity, unit_price = :unit_price, expiry_date = :expiry_date";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit_price = htmlspecialchars(strip_tags($this->unit_price));
        $this->expiry_date = htmlspecialchars(strip_tags($this->expiry_date));

        // Bind
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':expiry_date', $this->expiry_date);

        if ($stmt->execute()) {
            $this->stock_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read stock by ID
    public function readById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE stock_id = :stock_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stock_id', $this->stock_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Read stock by Product ID
    public function readByProductId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update stock quantity and details
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET quantity = :quantity, unit_price = :unit_price, expiry_date = :expiry_date 
                  WHERE stock_id = :stock_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit_price = htmlspecialchars(strip_tags($this->unit_price));
        $this->expiry_date = htmlspecialchars(strip_tags($this->expiry_date));
        $this->stock_id = htmlspecialchars(strip_tags($this->stock_id));

        // Bind
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':expiry_date', $this->expiry_date);
        $stmt->bindParam(':stock_id', $this->stock_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete stock entry
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE stock_id = :stock_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stock_id', $this->stock_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Static: Get total stock count for a product
    public static function getTotalStockByProductId($db, $product_id) {
        $query = "SELECT SUM(quantity) AS total_quantity FROM stock WHERE product_id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total_quantity'] ?? 0;
    }

    // Static: Get all stock entries
    public static function getAll($db) {
        $query = "SELECT * FROM stock";
        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Static: Get stock items nearing expiry
    public static function getNearingExpiry($db, $days = 30) {
        $query = "SELECT * FROM stock WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters
    public function getStockId() {
        return $this->stock_id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getUnitPrice() {
        return $this->unit_price;
    }

    public function getExpiryDate() {
        return $this->expiry_date;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setStockId($stock_id) {
        $this->stock_id = $stock_id;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function setUnitPrice($unit_price) {
        $this->unit_price = $unit_price;
    }

    public function setExpiryDate($expiry_date) {
        $this->expiry_date = $expiry_date;
    }
}
?>

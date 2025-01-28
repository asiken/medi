<?php
class OrderItem {
    private $conn;
    private $table_name = 'order_items';

    public $order_item_id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create Order Item
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET order_id=:order_id, product_id=:product_id, quantity=:quantity, price=:price";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->order_id = htmlspecialchars(strip_tags($this->order_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->price = htmlspecialchars(strip_tags($this->price));

        // Bind
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':price', $this->price);

        if ($stmt->execute()) {
            $this->order_item_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read Order Item by ID
    public function readById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE order_item_id = :order_item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_item_id', $this->order_item_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Read All Order Items by Order ID
    public function readByOrderId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update Order Item
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET product_id = :product_id, quantity = :quantity, price = :price WHERE order_item_id = :order_item_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->order_item_id = htmlspecialchars(strip_tags($this->order_item_id));

        // Bind
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':order_item_id', $this->order_item_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Order Item
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_item_id = :order_item_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_item_id', $this->order_item_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete All Order Items by Order ID
    public function deleteByOrderId() {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->order_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Static: Get Total Price of an Order
    public static function getTotalPriceByOrderId($db, $order_id) {
        $query = "SELECT SUM(price * quantity) AS total_price FROM order_items WHERE order_id = :order_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total_price'] ?? 0;
    }

    // Static: Get All Order Items
    public static function getAllOrderItems($db) {
        $query = "SELECT * FROM order_items";
        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters
    public function getOrderItemId() {
        return $this->order_item_id;
    }

    public function getOrderId() {
        return $this->order_id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setOrderItemId($order_item_id) {
        $this->order_item_id = $order_item_id;
    }

    public function setOrderId($order_id) {
        $this->order_id = $order_id;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function setPrice($price) {
        $this->price = $price;
    }
}
?>

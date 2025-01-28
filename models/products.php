<?php
class Product {
    private $conn;
    private $table_name = 'products';

    public $product_id;
    public $category_id;
    public $name;
    public $description;
    public $price;
    public $image_path;
    public $stock_quantity;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create Product
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET category_id=:category_id, name=:name, description=:description, price=:price, image_path=:image_path, stock_quantity=:stock_quantity";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));

        // Bind
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':stock_quantity', $this->stock_quantity);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read Product by ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update Product
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET category_id = :category_id, name = :name, description = :description, price = :price, image_path = :image_path, stock_quantity = :stock_quantity WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));

        // Bind
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':stock_quantity', $this->stock_quantity);
        $stmt->bindParam(':product_id', $this->product_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Product
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $this->product_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

?>

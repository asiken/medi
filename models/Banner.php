<?php
class Banner {
    private $conn;
    private $table_name = 'banners';

    public $banner_id;
    public $title;
    public $image_url;
    public $link;
    public $position;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new banner
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title = :title, image_url = :image_url, link = :link, position = :position, is_active = :is_active";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->link = htmlspecialchars(strip_tags($this->link));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));

        // Bind
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':link', $this->link);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->banner_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read all banners
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY position ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read a single banner by ID
    public function readById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE banner_id = :banner_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':banner_id', $this->banner_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a banner
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, image_url = :image_url, link = :link, position = :position, is_active = :is_active 
                  WHERE banner_id = :banner_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->link = htmlspecialchars(strip_tags($this->link));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
        $this->banner_id = htmlspecialchars(strip_tags($this->banner_id));

        // Bind
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':link', $this->link);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':banner_id', $this->banner_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete a banner
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE banner_id = :banner_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':banner_id', $this->banner_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Static: Get all active banners
    public static function getActiveBanners($db) {
        $query = "SELECT * FROM banners WHERE is_active = 1 ORDER BY position ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Static: Count total banners
    public static function countBanners($db) {
        $query = "SELECT COUNT(*) AS total FROM banners";
        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // Getters
    public function getBannerId() {
        return $this->banner_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getImageUrl() {
        return $this->image_url;
    }

    public function getLink() {
        return $this->link;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getIsActive() {
        return $this->is_active;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setBannerId($banner_id) {
        $this->banner_id = $banner_id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setImageUrl($image_url) {
        $this->image_url = $image_url;
    }

    public function setLink($link) {
        $this->link = $link;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function setIsActive($is_active) {
        $this->is_active = $is_active;
    }
}
?>

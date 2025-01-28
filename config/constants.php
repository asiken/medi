<?php
// Constants

define('UPLOAD_DIR', __DIR__ . '/../uploads/'); // Upload directory for prescription and other files
define('BASE_URL', 'http://localhost/OnlineMedicineStore/'); // Base URL of the application

// Allowed file types for uploads
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);

define('MAX_FILE_SIZE', 5 * 1024 * 1024); // Max upload size: 5MB

?>

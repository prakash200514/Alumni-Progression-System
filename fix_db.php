<?php
require_once 'db_connect.php';

function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$table = 'students';
$messages = [];

// 1. Handle id_photo
if (columnExists($conn, $table, 'id_photo')) {
    $messages[] = "'id_photo' column already exists.";
} else {
    if (columnExists($conn, $table, 'photo')) {
        // Rename photo to id_photo
        if ($conn->query("ALTER TABLE `$table` CHANGE `photo` `id_photo` VARCHAR(255)")) {
             $messages[] = "Renamed 'photo' to 'id_photo'.";
        } else {
             $messages[] = "Error renaming 'photo': " . $conn->error;
        }
    } else {
        // Add id_photo
        if ($conn->query("ALTER TABLE `$table` ADD COLUMN `id_photo` VARCHAR(255)")) {
             $messages[] = "Added 'id_photo' column.";
        } else {
             $messages[] = "Error adding 'id_photo': " . $conn->error;
        }
    }
}

// 2. Handle working_proof
if (columnExists($conn, $table, 'working_proof')) {
    $messages[] = "'working_proof' column already exists.";
} else {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `working_proof` TEXT")) {
         $messages[] = "Added 'working_proof' column.";
    } else {
         $messages[] = "Error adding 'working_proof': " . $conn->error;
    }
}

// 3. Handle signature
if (columnExists($conn, $table, 'signature')) {
    $messages[] = "'signature' column already exists.";
} else {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `signature` TEXT")) {
         $messages[] = "Added 'signature' column.";
    } else {
         $messages[] = "Error adding 'signature': " . $conn->error;
    }
}

echo implode("\n", $messages);
$conn->close();
?>

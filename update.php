<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "StarObservatory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tableName = $_POST['tableName'];
$columnName = $_POST['columnName'];
$newValue = $_POST['newValue'];
$rowId = $_POST['rowId'];

// Check and add date_update column if it doesn't exist
$sql = "SELECT COUNT(*) AS column_exist 
        FROM information_schema.COLUMNS 
        WHERE TABLE_NAME='$tableName' AND COLUMN_NAME='date_update'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['column_exist'] == 0) {
    $sql = "ALTER TABLE $tableName ADD COLUMN date_update DATETIME";
    if ($conn->query($sql) === TRUE) {
        echo "Column date_update added successfully. ";
    } else {
        echo "Error adding column: " . $conn->error;
    }
}

// Create trigger if it doesn't exist
$triggerName = "update_check_" . $tableName;
$sql = "DROP TRIGGER IF EXISTS $triggerName;";
$conn->query($sql);

$sql = "CREATE TRIGGER $triggerName BEFORE UPDATE ON $tableName
        FOR EACH ROW
        BEGIN
            SET NEW.date_update = NOW();
        END;";
if ($conn->query($sql) === TRUE) {
    echo "Trigger created successfully. ";
} else {
    echo "Error creating trigger: " . $conn->error;
}

// Update the table
$sql = "UPDATE $tableName SET $columnName='$newValue' WHERE id=$rowId";
if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>

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

$table1 = $_POST['table1'];
$table2 = $_POST['table2'];

// Create procedure if it doesn't exist
$procedureName = "CombineTables";
$sql = "DROP PROCEDURE IF EXISTS $procedureName;";
$conn->query($sql);

$sql = "CREATE PROCEDURE $procedureName(IN table1 VARCHAR(255), IN table2 VARCHAR(255))
        BEGIN
            SET @query = CONCAT('SELECT t1.id, t1.type, t1.accuracy, t1.quantity, t1.time, t1.date, t1.note, t1.date_update, t1.earth_position, t1.sun_position, t1.moon_position,
                                 t2.id AS id2, t2.type AS type2, t2.accuracy AS accuracy2, t2.quantity AS quantity2, t2.time AS time2, t2.date AS date2, t2.note AS note2, t2.date_update AS date_update2,
                                 t2.earth_position AS earth_position2, t2.sun_position AS sun_position2, t2.moon_position AS moon_position2
                              FROM $table1 t1
                              JOIN $table2 t2 ON t1.id = t2.id')";
            PREPARE stmt FROM @query;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END;";
$conn->query($sql);

// Call the procedure
$sql = "CALL $procedureName('$table1', '$table2')";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Prepare the INSERT statement for CombinedResults table
    $insertSql = "INSERT INTO CombinedResults (id, type, accuracy, quantity, time, date, note, date_update, earth_position, sun_position, moon_position)
                  VALUES ";
    $values = array();

    // Fetch rows and prepare insert values
    while($row = $result->fetch_assoc()) {
        // Handle NULL values or set defaults as needed
        $id = isset($row['id']) ? $row['id'] : '';
        $type = isset($row['type']) ? $row['type'] : '';
        $accuracy = isset($row['accuracy']) ? $row['accuracy'] : 0.0;
        $quantity = isset($row['quantity']) ? $row['quantity'] : 0;
        $time = isset($row['time']) ? $row['time'] : '';
        $date = isset($row['date']) ? $row['date'] : '';
        $note = isset($row['note']) ? $row['note'] : '';
        $date_update = isset($row['date_update']) ? $row['date_update'] : date('Y-m-d H:i:s'); // Use current date if date_update is NULL
        $earth_position = isset($row['earth_position']) ? $row['earth_position'] : '';
        $sun_position = isset($row['sun_position']) ? $row['sun_position'] : '';
        $moon_position = isset($row['moon_position']) ? $row['moon_position'] : '';

        // Prepare values for the INSERT statement
        $values[] = "('$id', '$type', '$accuracy', '$quantity', '$time', '$date', '$note', '$date_update', '$earth_position', '$sun_position', '$moon_position')";
    }

    // Join values and execute INSERT statement
    $insertSql .= implode(', ', $values);

    if ($conn->query($insertSql) === TRUE) {
        echo "Combined data inserted into CombinedResults table successfully";
    } else {
        echo "Error inserting combined data: " . $conn->error;
    }
} else {
    echo "0 results";
}

$conn->close();
?>

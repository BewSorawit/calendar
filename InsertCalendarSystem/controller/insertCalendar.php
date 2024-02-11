<?php
// Include the connection file
require("connection_connect.php");

// Delete all records from the 'date' table
$deleteQuery = "DELETE FROM date";
mysqli_query($conn, $deleteQuery);

// Open uploaded CSV file with read-only mode
$csvFile = fopen($_FILES['file']['tmp_name'], 'r');

// Skip the first line
fgetcsv($csvFile);

// Parse data from CSV file line by line        
while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE) {
    // Extract data from CSV row
    list($idDate, $date, $numWeek, $nameOf, $continuousHoliday, $nameDay, $checkHoliday, $nameType) = $getData;

    // Get corresponding IDs from other tables
    $idNoWeek = $numWeek;
    $idName = getIdFromTable($conn, 'nameof', 'idName', 'nameOf', $nameOf);
    $idCheckCon = getIdFromTable($conn, 'checkcon', 'idCheckCon', 'continuousHoliday', $continuousHoliday);
    $idDay = getIdFromTable($conn, 'dayofweek', 'idDay', 'nameDay', $nameDay);
    $idCheckRest = getIdFromTable($conn, 'checkrest', 'idCheckRest', 'checkHoliday', $checkHoliday);
    $idType = getIdFromTable($conn, 'typerest', 'idType', 'nameType', $nameType);

    // Check if any foreign key IDs are null
    if ($idName !== null && $idCheckCon !== null && $idDay !== null && $idCheckRest !== null && $idType !== null) {
        // Insert the data into the 'date' table
        $insertQuery = "INSERT INTO date (idDate, date, idNoWeek, idName, idCheckCon, idDay, idCheckRest, idType) 
                        VALUES ('$idDate', '$date', '$idNoWeek', '$idName', '$idCheckCon', '$idDay', '$idCheckRest', '$idType')";

        // Execute the query
        mysqli_query($conn, $insertQuery);
    } else {
        // Log or handle the error for the invalid row (missing foreign key, etc.)
        // You can add more specific error handling here based on your requirements
        // For example, log the error message
        error_log("Invalid row: ID Name: $idName, ID Check Con: $idCheckCon, ID Day: $idDay, ID Check Rest: $idCheckRest, ID Type: $idType");
    }
}

// Close opened CSV file
fclose($csvFile);

// Refresh page after 2 seconds
header("refresh:2; url=../views/home.php");

// Close the database connection
require("connection_close.php");

// Function to get ID from a table based on a given value
function getIdFromTable($conn, $tableName, $idColumnName, $valueColumnName, $value) {
    // Escape the value to prevent SQL injection and handle special characters
    $escapedValue = mysqli_real_escape_string($conn, $value);

    // Build and execute the query
    $sql = "SELECT $idColumnName FROM $tableName WHERE $valueColumnName = '$escapedValue'";
    $result = mysqli_query($conn, $sql);

    // Check for errors in the SQL query
    if (!$result) {
        die('Error in SQL query: ' . mysqli_error($conn));
    }

    // Fetch the result and free the result set
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    return $row[$idColumnName];
}
?>

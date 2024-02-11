<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    // Insert the data into the 'date' table
    $insertQuery = "INSERT INTO date (idDate, date, idNoWeek, idName, idCheckCon, idDay, idCheckRest, idType) 
                    VALUES ('$idDate', '$date', '$idNoWeek', '$idName', '$idCheckCon', '$idDay', '$idCheckRest', '$idType')";

    // Execute the query
    mysqli_query($conn, $insertQuery);
}

// Close opened CSV file
fclose($csvFile);

// Display success message using SweetAlert
echo "<script>
    $(document).ready(function() {
        Swal.fire({
            title: 'Success',
            text: 'Data added successfully!',
            icon: 'success',
            timer: 5000,
            showConfirmButton: false
        });
    })
</script>";

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

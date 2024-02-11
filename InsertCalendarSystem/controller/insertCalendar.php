<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
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
    // Get row data
    $idDate = $getData[0];
    $csvDate = $getData[1];
    $numWeek = $getData[2];
    $nameOf = $getData[3];
    $continuousHoliday = $getData[4];
    $nameDay = $getData[5];
    $checkHoliday = $getData[6];
    $nameType = $getData[7];

    // Convert the CSV date format to MySQL format
    $dateObj = DateTime::createFromFormat('Y-m-d', $csvDate);
    $date = $dateObj ? $dateObj->format('Y-m-d') : null;

    // Get corresponding IDs from other tables
    $idNoWeek = $numWeek; // Assuming numWeek is equivalent to idNoWeek
    $idName = getIdFromTable($conn, 'nameof', 'idName', 'nameOf', $nameOf);
    $idCheckCon = getIdFromTable($conn, 'checkcon', 'idCheckCon', 'continuousHoliday', $continuousHoliday);
    $idDay = getIdFromTable($conn, 'dayofweek', 'idDay', 'nameDay', $nameDay);
    $idCheckRest = getIdFromTable($conn, 'checkrest', 'idCheckRest', 'checkHoliday', $checkHoliday);
    $idType = getIdFromTable($conn, 'typerest', 'idType', 'nameType', $nameType);

    // Check if all required IDs are valid
    if ($idName !== null && $idCheckCon !== null && $idDay !== null && $idCheckRest !== null && $idType !== null) {
        // Insert the data into the 'date' table
        $insertQuery = "INSERT INTO date 
                        SET idDate = '$idDate', 
                            date = '$date', 
                            idNoWeek = '$idNoWeek', 
                            idName = '$idName', 
                            idCheckCon = '$idCheckCon', 
                            idDay = '$idDay', 
                            idCheckRest = '$idCheckRest', 
                            idType = '$idType'";

        mysqli_query($conn, $insertQuery);
    } else {
        // Log or handle the error for the invalid row (missing foreign key, etc.)
        // You can add more specific error handling here based on your requirements
    }
}

// Close opened CSV file
fclose($csvFile);

echo "<script>
    $(document).ready(function() {
        Swal.fire({
            title: 'สำเร็จ',
            text: 'เพิ่มข้อมูลสำเร็จ!',
            icon: 'success',
            timer: 5000,
            showConfirmButton: false
        });
    })
</script>";

header("refresh:1; url=../views/home.php");

require("connection_close.php");

// Function to get ID from a table based on a given value
function getIdFromTable($conn, $tableName, $idColumnName, $valueColumnName, $value) {
    // Escape the value to handle apostrophes and other special characters
    $escapedValue = mysqli_real_escape_string($conn, $value);

    $sql = "SELECT $idColumnName FROM $tableName WHERE $valueColumnName = '$escapedValue'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        // Handle the SQL error, log, or display an error message
        echo 'Error in SQL query: ' . mysqli_error($conn);
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return $row ? $row[$idColumnName] : null;
}

?>

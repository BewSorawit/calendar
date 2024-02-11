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

$idDateCounter = 1; // Initialize the counter

// Parse data from CSV file line by line        
while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE) {
    // Get row data
    $idDate = $idDateCounter;
    $dateObj = DateTime::createFromFormat('m/d/Y', $getData[0]);
    $date = $dateObj ? $dateObj->format('Y-m-d') : null;
    $idNoWeek = $getData[1];
    $idName = $getData[2];
    $idCheckCon = $getData[3];
    $idDay = $getData[4];
    $idCheckRest = $getData[5];
    $idType = $getData[6];

    // If event already exists in the database with the same idDate
    $query = "SELECT idDate FROM date WHERE idDate = '$idDate'";
    $check = mysqli_query($conn, $query);

    if ($check->num_rows > 0) {
        mysqli_query($conn, "UPDATE date SET date = '$date', idNoWeek = '$idNoWeek', idName = '$idName', idCheckCon = '$idCheckCon', idDay = '$idDay', idCheckRest = '$idCheckRest', idType = '$idType' WHERE idDate = '$idDate'");
    } else {
        mysqli_query($conn, "INSERT INTO date (idDate, date, idNoWeek, idName, idCheckCon, idDay, idCheckRest, idType) VALUES ('$idDate', '$date', '$idNoWeek', '$idName', '$idCheckCon', '$idDay', '$idCheckRest', '$idType')");
    }

    $idDateCounter++; // Increment the counter by 1 for the next iteration
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

header("refresh:2; url=../views/home.php");

require("connection_close.php");
?>

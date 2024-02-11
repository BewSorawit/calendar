<?php
include('condb.php');

// Delete all records from the 'date' table
$deleteQuery = "DELETE FROM date";
mysqli_query($conn, $deleteQuery);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == UPLOAD_ERR_OK) {
        $csvFile = $_FILES["csvFile"]["tmp_name"];

        // Process the CSV file and insert data into the 'date' table
        $handle = fopen($csvFile, "r");

        if ($handle === false) {
            die("Error opening the CSV file.");
        }

        $idDateCounter = 0; // Initialize the counter

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $idDate = $idDateCounter;
            $dateObj = DateTime::createFromFormat('m/d/Y', $data[1]);
            $date = $dateObj ? $dateObj->format('Y-m-d') : null;
            $idNoWeek = $data[2];
            $idName = $data[3];
            $idCheckCon = $data[4];
            $idDay = $data[5];
            $idCheckRest = $data[6];
            $idType = $data[7];

            $sql = "INSERT INTO date (idDate, date, idNoWeek, idName, idCheckCon, idDay, idCheckRest, idType) 
                    VALUES ('$idDate', '$date', '$idNoWeek', '$idName', '$idCheckCon', '$idDay', '$idCheckRest', '$idType')";

            if ($conn->query($sql) === false) {
                echo "Error: " . $conn->error;
            }

            $idDateCounter++; // Increment the counter by 1 for the next iteration
        }

        fclose($handle);
        echo "Data uploaded successfully.";
    } else {
        echo "Error uploading the file. Debugging information:<br>";
        print_r($_FILES); // Output additional information about the file upload
    }
}

$conn->close();
?>

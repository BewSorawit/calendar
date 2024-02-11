<?php
include('condb.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirmDelete"])) {
    $sql = "DELETE FROM date";

    if ($conn->query($sql) === true) {
        echo "All dates deleted successfully.";
    } else {
        echo "Error deleting dates: " . $conn->error;
    }
}

$conn->close();
?>

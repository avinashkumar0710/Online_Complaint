<!DOCTYPE html>
<html>
<head>
    <title>Your Page Title</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Mukta&family=Prompt&family=Roboto&family=Signika+Negative:wght@500&display=swap"
        rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>PLEDGE</h1>
    <img src="path/to/your/photo.jpg" alt="Description of the Photo">
    
    <form method="post" action="process.php">
        <label for="employeeNumber">Employee Number:</label>
        <input type="text" id="employeeNumber" name="employeeNumber" placeholder="Enter employee number" required>
        <br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
<?php
$connection = new mysqli("localhost", "root", "", "nspcl_website_test");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$query = "SELECT COUNT(*) AS total_count FROM pledge";
$result = $connection->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalCount = $row["total_count"];
    echo "Total pledges: " . $totalCount;
} else {
    echo "No pledges found.";
}

$connection->close();

?>

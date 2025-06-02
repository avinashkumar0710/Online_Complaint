<?php
// Retrieve the form data
$comments = $_POST['comments'];
$smiley = $_POST['smiley'];
$name = $_POST['postedBy'];
$dept = $_POST['userDept'];
$desig = $_POST['userDesign'];
$plant = $_POST['plant'];


// Get the client's IP address
$ipAddress = $_SERVER['REMOTE_ADDR'];
echo "<pre>";
echo "IP Address: " . $ipAddress;
echo "</pre>";
// Database connection parameters
$serverName = "192.168.100.240";
$connectionInfo = array(
    "Database" => "complaint",
    "UID" => "sa",
    "PWD" => "Intranet@123"
);           

// Establish the database connection
$conn = sqlsrv_connect($serverName, $connectionInfo);
echo $plant;
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Prepare the SQL statement to get the maximum ID
$getQIDSql = "SELECT MAX(ID) AS id FROM OCMSComment";
$getQIDStmt = sqlsrv_query($conn, $getQIDSql);

if ($getQIDStmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Get the maximum ID value
$id = 0;
if ($row = sqlsrv_fetch_array($getQIDStmt, SQLSRV_FETCH_ASSOC)) {
    if (!is_null($row['id'])) {
        $id = intval($row['id']) + 1;
    } else {
        $id = 1; // FIRST NUMBER
    }
}

// Prepare the SQL statement to insert the feedback
$sql = "INSERT INTO [Complaint].[dbo].[OCMSComment] ([ID], [C_date], [comments], [smiley], [name], [dept], [desig], [commentIP], [Plant]) 
        VALUES (?, GETDATE(), ?, ?, ?, ?, ?, ?, ?)";

$params = array($id, $comments, $smiley, $name, $dept, $desig, $ipAddress, $plant);
$stmt = sqlsrv_prepare($conn, $sql, $params);


if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Execute the SQL statement
if (sqlsrv_execute($stmt) === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Close the database connection
sqlsrv_close($conn);

// Redirect to a success page or display a success message
echo "<script>alert('Feedback submitted successfully!');</script>";
header("location:feedback.php");
?>

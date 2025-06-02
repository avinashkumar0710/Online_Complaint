<?php
session_start();

$serverName = "192.168.100.240";
$connectionInfo = array(
    "Database" => "complaint",
    "UID" => "sa",
    "PWD" => "Intranet@123"
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    $username = $_POST['emp_num'];
    $password = $_POST['passwd'];

    $sql = "SELECT * FROM EA_webuser_tstpp WHERE emp_num = ? AND passwd = ? and status in ('A','O','S')";

    $params = array($username, $password);
    $stmt = sqlsrv_query($conn, $sql, $params);
    $row = sqlsrv_fetch_array($stmt);

    if ($row != null) {
        $_SESSION['emp_num'] = $username;

        $loginActivity = "INSERT INTO login_details (emp_num, login_time, logout_time) VALUES (?, GETDATE(), NULL)";
        $params = array($username);
        $result = sqlsrv_query($conn, $loginActivity, $params);
    } else {
        $_SESSION['login_error'] = true; // Set a session variable to indicate login error
    }
}

header("location: index.php");
exit;
?>

<!DOCTYPE html>
<html>

<head>
    <title>New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<?php include 'Complaintheader.php';?>
<?php
session_start();
$serverName = "192.168.100.240";
$connectionOptions = array(
    "Database" => "complaint",
    "UID" => "sa",
    "PWD" => "Intranet@123"
);
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}


$userId = $_SESSION['emp_num'];
//find plant & Department code
$plant_dptcode = "SELECT plant, dept_code FROM EA_webuser_tstpp WHERE emp_num = '$userId'";
$result1 = sqlsrv_query($conn, $plant_dptcode);

if ($result1) {
    // Fetch each row from the result set
    while ($row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
        $plant = $row1['plant'];
        $dept_code = $row1['dept_code'];
        //print $plant;
        //print $dept_code;
    }
}
?>

<?php
// FUNCTION SECTIONS

function shownewrequest() {
    // OPEN SQL SERVER INTRANET DATABASE CONNECTION
    $serverName = "192.168.100.240";
    $connectionOptions = array(
        "Database" => "complaint",
        "UID" => "sa",
        "PWD" => "Intranet@123"
    );

    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // CHECK FOR VALIDITY OF OLD PASSWORD
    $strCheckOldPwd = "SELECT emp_num, emp_name " .
        "FROM EA_webuser_tstpp " .
        "WHERE dept_code is null and access is null and status='U' order by emp_num";

    // Execute the SQL query
    $objRS = sqlsrv_query($conn, $strCheckOldPwd);

    if ($objRS === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($objRS) === false) {
        echo "<h3 align=center>NO REQUEST IS PENDING</h3>";
    } else {
        echo "<ul>";
        $ct = 1;
        while ($row = sqlsrv_fetch_array($objRS, SQLSRV_FETCH_ASSOC)) {
            $empno = $row["emp_num"];
            $empname = $row["emp_name"];
            echo "<li>" . $ct . ". &nbsp; <a href='./user_request.php?pageaction=show&empno=" . $empno . "' onmousemove=\"window.status='New user Requests'\" onmouseover=\"window.status='New user Requests'\">New user Requests for emp no " . $empno . " name " . $empname . "</a></li>";
            $ct++;
        }
        echo "</ul>";
    }

    // Close the recordset
    sqlsrv_free_stmt($objRS);

    // Close the connection
    sqlsrv_close($conn);
}

// Call the function
shownewrequest();
?>
 <?php include 'footer.php';?>
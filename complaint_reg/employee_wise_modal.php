<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Mukta&family=Prompt&family=Roboto&family=Signika+Negative:wght@500&display=swap"
        rel="stylesheet">
    <style>
    /* Center align the table */
    table {
        border-collapse: collapse;
    }

    /* Add some padding and borders to table cells */
    table td {
        padding: 5px;
        border: 1px solid #ccc;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 8px;
    }

    /* Style the heading */
    h3 {
        text-align: center;
        text-decoration: underline;
        margin-bottom: 20px;
    }

    /* Style the complaint status */
    .complaint-status {
        font-weight: bold;
    }

    /* Style the complaint details */
    .complaint-details {
        margin-bottom: 10px;
    }

    #currentDateTime {
        font-family: Arial, sans-serif;
        /* Specify the desired font family */
        font-size: 18px;
        /* Specify the desired font size */
        color: #ff0000;
        /* Specify the desired color */
    }

    #submitBtnContainer {
        text-align: center;
    }

    body {
        font-family: 'Signika Negative', sans-serif;
    }
    </style>
</head>


<?php
session_start();
 // Set up connection parameters
 $serverName = "192.168.100.240";
    $connectionOptions = array(
        "Database" => "complaint",
        "UID" => "sa",
        "PWD" => "Intranet@123"
    );

 // Establish a connection
 $conn = sqlsrv_connect($serverName, $connectionOptions);
 if ($conn === false) {
     die(print_r(sqlsrv_errors(), true));
 }
 $emp_no = $_SESSION["emp_num"];
 //$plant = $_SESSION["Plant"];
$sql  = "select Plant from [Complaint].[dbo].[EA_webuser_tstpp] where emp_num='$_SESSION[emp_num]'";

$params = array($emp_no);

// Prepare and execute the query
$stmt = sqlsrv_query($conn, $sql, $params);

// Check if the query was successful and if there are any matching records
if ($stmt && sqlsrv_has_rows($stmt)) {
    // Fetch the Plant value from the result set
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $plant = $row['Plant'];

    // Print the Plant value
    //echo "Plant: " . $plant;
} else {
    // Handle the case when no matching records are found or any other error
    echo "No records found or an error occurred.";
}

// Retrieve the compNo from the query string parameter
$compNo = $_GET['compNo'];
//$status= $_GET['status'];
//print "complaint no : " . $compNo;

$strSelComp = "SELECT CT.CompNo AS CompNo, CT.[Time] AS CompTime, CT.CompDate AS compDate, CT.CompOriginDeptID AS compOriginDeptID, CT.CompDeptID AS compDeptID, CT.compTypeID AS compTypeID,
        CT.Description AS Description, CT.Location AS Location, CT.ContactNo AS ContactNo, CT.CompUserID AS compUserID, CT.Status AS Status, CTM.deptID AS deptID, CTM.compTypeDesc AS compTypeDesc,
        CTM.deptDesc AS deptDesc, dbo.EA_webuser_tstpp.emp_name  
        FROM dbo.ComplaintTable CT
        INNER JOIN dbo.compTypeMas CTM ON CT.compTypeID = CTM.compTypeId AND CT.CompDeptID = CTM.deptID
        INNER JOIN dbo.EA_webuser_tstpp ON CT.CompUserID = dbo.EA_webuser_tstpp.emp_num
        WHERE CT.CompNo = " . $compNo;

$result = sqlsrv_query($conn, $strSelComp);

// Check if the query was successful
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (sqlsrv_has_rows($result)) {
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    $dept = $row['compOriginDeptID'];
    //print $dept;
}


$findDeprt = "SELECT Dept_id ,DeptName 
FROM EA_DeptCode_Mas 
WHERE Dept_id = '$dept' and Plant='$plant'";
$result1 = sqlsrv_query($conn, $findDeprt);

// Check if the query was successful
if ($result1 === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (sqlsrv_has_rows($result1)) {
    $row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC);
    $dept = $row1['DeptName'];
    //$plantshow= $row1['Plant'];
    //print $plantshow;
}

?>
<div class="container"><br>
    <form name="frmAddRemark" id="frmAddRemark" method="post"
        action="/deptCompRegister/attendComp.asp?compNo=<?php echo $_GET['compNo']; ?>">
        <input type="hidden" id="frmAction" name="frmAction">
        <table class="table table-bordered" border="0" align="center">
            <tr>
                <td colspan="3">
                    <h3 align="center">
                        <input type="hidden" id="compNo" name="compNo" value="<?php echo (int)$_GET['compNo']; ?>">
                        <u>Details of complaint Number: <?php echo $_GET['compNo']; ?></u>
                    </h3>
                </td>
            </tr>
            <tr>
                <td valign="top"><b>Complaint Date</b></td>
                <td valign="top">:&nbsp;</td>
                <td><?php echo $row['compDate']->format('Y-m-d'); ?>&nbsp;&nbsp;<font color="blue"> Time:<font
                            color="red">
                            [<?php echo $row['CompTime']; ?>]</td>
            </tr>
            <tr>
                <td valign="top"><b>Submitted By</b></td>
                <td valign="top">:&nbsp;</td>
                <td><?php echo $row["emp_name"]; ?>(<?php echo $row["compUserID"]; ?>)</td>
            </tr>
            <tr>
                <td valign="top"><b>User Department</b></td>
                <td valign="top">:&nbsp;</td>
                <td><?php echo $row1["DeptName"]; ?>(<?php echo $row["compOriginDeptID"]; ?>)</td>
            </tr>
            <tr>
                <td valign="top"><b>Complaint For Deptt.</b></td>
                <td valign="top">:&nbsp;</td>
                <td><input type="hidden" id="compForDeptID" name="compForDeptID"
                        value="<?php echo $row["compDeptID"]; ?>"><?php echo $row["deptDesc"]; ?></td>
            </tr>

            <!----Complaint Type----->
            <tr>
                <td valign="top"><b>Complaint Type</b></td>
                <td valign="top">:&nbsp;</td>
                <td>
                    <b><?php echo strtoupper($row["compTypeDesc"]); ?></b>
                </td>
            </tr>
            <tr>
                <td><b>Location</b></td>
                <td valign="top">:&nbsp;</td>
                <td><?php echo $row["Location"]; ?></td>
            </tr>
            <tr>
                <td><b>Contact Number</b></td>
                <td valign="top">:&nbsp;</td>
                <td><?php echo strtoupper($row["ContactNo"] !== null ? $row["ContactNo"] : "null"); ?></td>
            </tr>

            <tr>
                <td valign="top"><b>Complaint Description</b></td>
                <td valign="top">:&nbsp;</td>
                <td><b><?php echo strtoupper($row["Description"]); ?></b></td>
            </tr>
            <tr>
                <td valign="top" class="complaint-status">Complaint Status</td>
                <td valign="top">:&nbsp;</td>
                <td><?php
                if (strtoupper($row["Status"]) == "N") {
                    echo "Complaint is <b>New</b>.";
                } elseif (strtoupper($row["Status"]) == "S") {
                    echo "Complaint is <b>Seen</b> by Complaint Department Executive.";
                } elseif (strtoupper($row["Status"]) == "C") {
                    echo "Complaint is <b>Attended</b>.";
                } elseif (strtoupper($row["Status"]) == "R") {
                    echo "Complaint is <b>Remarked</b>.";
                }
            ?></td>
        </table>
    </form>
    <?php
    echo '<center>';
    echo '<div class="container">';
    echo '<table class="table table-bordered" border="0" align="center">';
    echo '<style>';
    echo 'body { font-family:Signika Negative, sans-serif;}';
    echo 'table { border-collapse: collapse; }';
    echo 'th, td { border: 1px solid #ccc; padding: 8px; }';
    echo 'th { background-color: #f2f2f2; font-weight: bold; line-height: 1.5; }';
    echo 'tr { line-height: 1.2; }';
    echo '</style>';

    $query = "
SELECT CAD.AttendedByUserID, CAD.AttendedDate, CAD.Remarks, EWT.emp_name
FROM [complaint].[dbo].[ComplaintAttendDet] CAD
JOIN [Complaint].[dbo].[EA_webuser_tstpp] EWT ON CAD.AttendedByUserID = EWT.emp_num
WHERE CAD.CompNo = '$compNo'
";

$result = sqlsrv_query($conn, $query);

if ($result !== false) {
echo '<table>';
echo '<tr>';
echo '<th style="background-color: #5DADE2;">Remarks By</th>';
echo '<th style="background-color: #5DADE2;">Remarks Date</th>';
echo '<th style="background-color: #5DADE2;">Remarks</th>';
echo '</tr>';

while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $AttendedByUserID = $row['AttendedByUserID'];
    $AttendedDate = $row['AttendedDate']->format('Y-m-d');
    $Remarks = $row['Remarks'];
    $empName = $row['emp_name'];

    echo '<tr>';
    echo '<td style="padding: 8px;">' . $empName . '   </td>';
    echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
    echo '<td style="padding: 8px;">' . $Remarks . '</td>';
    echo '</tr>';
}

echo '</table>';
} 
?>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Mukta&family=Prompt&family=Roboto&family=Signika+Negative:wght@500&display=swap"
        rel="stylesheet">
    <style>
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

$strSelComp = "SELECT     CT.CompNo as CompNo, CT.CompDate as compDate, 
CT.CompOriginDeptID  as compOriginDeptID, CT.CompDeptID  as compDeptID,
CT.compTypeID as compTypeID, 
CT.Description  as Description, CT.ContactNo  as ContactNo, CT.Location  as Location, 
CT.CompUserID as compUserID, CT.Status as Status, CTM.deptID as deptID, 
CTM.compTypeDesc as compTypeDesc, CTM.deptDesc  as deptDesc 
FROM         ComplaintTable CT, compTypeMas CTM 
WHERE CT.compTypeID = CTM.compTypeId AND 
CT.CompDeptID = CTM.deptID AND
CT.CompNo= " . $compNo;

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


// Now $empno has a length of 9 characters with "00" added if necessary
//echo $empno;

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
<div class="container">
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
                <td><?php echo $row['compDate']->format('Y-m-d'); ?>&nbsp;&nbsp;<font color="blue"> </td>
            </tr>
            <tr>
                <td valign="top"><b>Submitted By</b></td>
                <td valign="top">:&nbsp;</td>
                <?php
$compUserID = $row["compUserID"];

// Select emp_name from EA_webuser_tstpp where emp_num matches compUserID
$selectEmpNameQuery = "SELECT emp_name FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$compUserID'";
$empNameResult = sqlsrv_query($conn, $selectEmpNameQuery);

if ($empNameResult) {
    $empNameRow = sqlsrv_fetch_array($empNameResult, SQLSRV_FETCH_ASSOC);
    $empName = isset($empNameRow["emp_name"]) ? $empNameRow["emp_name"] : null;
} else {
    echo "Error executing the query to retrieve emp_name: " . sqlsrv_errors();
}

if (empty($empName) && empty($compUserID)) {
    echo '<td>No data found</td>';
} else {
    echo '<td>' . ($empName ?: '&nbsp;') . ($compUserID ? ' (' . $compUserID . ')' : '') . '</td>';
}
?>


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
                    echo "Complaint is <b>Completed</b>.";
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

     if (strtoupper($row['Status']) == "R") {
        $statusR = "SELECT AttendedByUserID, AttendedDate, Remarks FROM [complaint].[dbo].[ComplaintAttendDet] WHERE CompNo = " . $compNo;
    
        // Execute the query
        $resultR = sqlsrv_query($conn, $statusR);
    
        if (sqlsrv_has_rows($resultR)) {
            echo '<tr>';
            echo '<td style="background-color: #58D68D;">Remarks By</td>';
            echo '<td style="background-color: #58D68D;">Remarks Date</td>';
            echo '<td style="background-color: #58D68D;">Remarks</td>';
            echo '</tr>';
    
            while ($row = sqlsrv_fetch_array($resultR, SQLSRV_FETCH_ASSOC)) {
                $empname = "SELECT CAD.AttendedByUserID, CAD.AttendedDate, CAD.Remarks, EWT.emp_name
                            FROM [complaint].[dbo].[ComplaintAttendDet] CAD
                            JOIN [Complaint].[dbo].[EA_webuser_tstpp] EWT ON CAD.AttendedByUserID = EWT.emp_num
                            WHERE CAD.CompNo= " . $compNo;
                $resultN = sqlsrv_query($conn, $empname);
                $rowN = sqlsrv_fetch_array($resultN, SQLSRV_FETCH_ASSOC);
                
                $AttendedByUserID = $row['AttendedByUserID'];
                $AttendedDate = $row['AttendedDate']->format('Y-m-d');
                $Remarks = $row['Remarks'];
                
                if ($rowN !== false) {
                    $empName = $rowN['emp_name'];
                    echo '<tr>';
                    echo '<td style="padding: 8px;">' . $empName . '</td>';
                    echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                    echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                    echo '</tr>';
                }
            }
        }
    }
    
    elseif (strtoupper($row['Status']) == "C") {
        $statusC = "SELECT AttendedByUserID, AttendedDate, Remarks FROM [complaint].[dbo].[ComplaintAttendDet] WHERE CompNo = " . $compNo;
    
        // Execute the query
        $resultC = sqlsrv_query($conn, $statusC);
    
        if (sqlsrv_has_rows($resultC)) {
            echo '<tr>';
            echo '<td style="background-color: #5DADE2;">Remarks By</td>';
            echo '<td style="background-color: #5DADE2;">Remarks Date</td>';
            echo '<td style="background-color: #5DADE2;">Remarks</td>';
            echo '</tr>';
    
            while ($row = sqlsrv_fetch_array($resultC, SQLSRV_FETCH_ASSOC)) {
                
    
                $empname = "SELECT CAD.AttendedByUserID, CAD.AttendedDate, CAD.Remarks, EWT.emp_name
                FROM [complaint].[dbo].[ComplaintAttendDet] CAD
                JOIN [Complaint].[dbo].[EA_webuser_tstpp] EWT ON CAD.AttendedByUserID = EWT.emp_num
                WHERE CAD.CompNo= " . $compNo;
                // echo '<script>';
                // echo 'alert("' . $empname . '");';
                // echo '</script>';
                $resultN = sqlsrv_query($conn, $empname);
                $rowN = sqlsrv_fetch_array($resultN, SQLSRV_FETCH_ASSOC);
                $AttendedByUserID = $row['AttendedByUserID'];
                $AttendedDate = $row['AttendedDate']->format('Y-m-d');
                $Remarks = $row['Remarks'];
                //$empName = $row['emp_name'];
                if ($rowN !== false) {
                    $empName = $rowN['emp_name'];
                    
    
                    echo '<tr>';
                    echo '<td style="padding: 8px;">' . $empName . ' </td>';
                    echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                    echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                    echo '</tr>';
                }
            }
        }
    }
    
?>
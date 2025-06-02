<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Mukta&family=Prompt&family=Roboto&family=Signika+Negative:wght@500&display=swap" rel="stylesheet">
<style>
     body {
        font-family: 'Signika Negative', sans-serif;
    }
    </style>

<?php
session_start();

 // Set up connection parameters
 $serverName = "192.168.100.240";
 $connectionOptions = array(
     "Database" => "Complaint",
     "UID" => "sa",
    "PWD" => "Intranet@123"
 );

 // Establish a connection
 $conn = sqlsrv_connect($serverName, $connectionOptions);
 if ($conn === false) {
     die(print_r(sqlsrv_errors(), true));
 }

// Retrieve the compNo from the query string parameter
$compNo = $_GET['compNo'];


$plantQuery = "SELECT Plant, emp_num,dept_code,emp_name FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$_SESSION[emp_num]'";
    $plantResult = sqlsrv_query($conn, $plantQuery);
    $userId = $_SESSION['emp_num'];

    if ($plantResult) {
        $row = sqlsrv_fetch_array($plantResult, SQLSRV_FETCH_ASSOC);
        $plant = $row['Plant'];
        $dept_code = $row['dept_code'];
        $emp_name = $row['emp_name'];
        //print $dept_code;
        //print $plant;
    }

    $findDeprt = "SELECT Dept_id ,DeptName 
    FROM EA_DeptCode_Mas 
    WHERE Dept_id = '$dept_code' and Plant='$plant'";
    $result1 = sqlsrv_query($conn, $findDeprt);
    
    // Check if the query was successful
    if ($result1 === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_has_rows($result1)) {
        $row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC);
        $dept = $row1['DeptName'];
        //$plantshow= $row1['Plant'];
        //print $dept;
    }

    // Get the emp_num from the $_SESSION variable (sanitize if necessary)
$emp_num = $_SESSION['emp_num'];

// Prepare the SQL query with placeholders for the parameters
$sql = "SELECT compTypeMas.deptDesc, deptId 
        FROM compTypeMas 
        JOIN EA_webuser_tstpp ON compTypeMas.deptID = EA_webuser_tstpp.dept_code 
        WHERE emp_num = ?";

// Prepare the statement
$params = array($emp_num);
$stmt = sqlsrv_prepare($conn, $sql, $params);

// Execute the query
if (sqlsrv_execute($stmt) === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch and store only the deptID values in an array
$deptIDs = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $deptIDs = $row['deptDesc'];
}

// Prepare the SQL query with the additional condition
$strSelComp = "SELECT CT.CompNo as CompNo, CT.CompDate as compDate, CT.CompOriginDeptID as compOriginDeptID, CT.CompDeptID as compDeptID, CT.compTypeID as compTypeID, 
CT.Description as Description, CT.ContactNo as ContactNo, CT.Location as Location, CT.CompUserID as compUserID, CT.Status as Status, CTM.deptID as deptID, 
CTM.compTypeDesc as compTypeDesc, CTM.deptDesc as deptDesc 
FROM ComplaintTable CT, compTypeMas CTM 
WHERE CT.compTypeID = CTM.compTypeId AND CT.CompDeptID = CTM.deptID AND CT.CompNo = " . $compNo;

// Execute the query
$result = sqlsrv_query($conn, $strSelComp);

// Check if the query was successful
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}


// Check if there is a row returned
if ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    // Fetch the row
    //$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    //$compOriginDeptID = $row['compOriginDeptID'];
    //print $compOriginDeptID;
    echo '<body>';
    echo '<center>';
    echo '<div class="container">';
    
    
    echo '<tr><br>';
    echo '<td><h3 align=center><u>Details of complaint Number : ' . $compNo . '</u></h3>';
    echo '</td></tr>';

    echo '<table class="table table-bordered" border=0 align=center>';
    echo '<tr>';
    echo '<td valign=top><b>Complaint Date  </b></td>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . ($row['compDate']->format('Y-m-d ')) . '</td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<td valign=top><b>Submitted By</b>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . $emp_name . '(' . $row['compUserID'] . ')</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td valign=top><b>User Department  </b>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . $row1['DeptName'] . ' (' . $row['compOriginDeptID'] . ')</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td valign=top><b>Complaint For Deptt.  </b>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . $row['deptDesc'] . '</td>';

    echo '<tr>';
    echo '<td valign=top><b>Complaint Type  </b>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . $row['compTypeDesc'] . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><b>Contact Number  </b>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . $row['ContactNo'] . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><b>Location  </b>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . $row['Location'] . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td valign=top><b>Complaint Description  </b>';
    echo '<td valign="top">:&nbsp;</td>';
    echo '<td>' . $row['Description'] . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td valign=top><b>Complaint Status  </b>';
    echo '<td valign="top">:&nbsp;</td>';
    
    if (strtoupper($row['Status']) == "N") {
        echo '<td>Complaint is <B>New</B>.</td>';
    } elseif (strtoupper($row['Status']) == "S") {
        echo '<td>Complaint is <B>Seen</B> by Complaint Department Executive.</td>';
    } elseif (strtoupper($row['Status']) == "C") {
        echo '<td>Complaint is <B>Completed</B>.</td>';
    } elseif (strtoupper($row['Status']) == "R") {
        echo '<td>Complaint is <B>Remarked</B>.</td>';
        // showRemarksToComplaint(Request.QueryString("compNo"));
        ($_GET['compNo']);
    }
    
    echo '</tr>';
    echo '</center>';
    echo '</table>';
    echo '</div>';

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
            $AttendedByUserID = $row['AttendedByUserID'];
            $AttendedDate = $row['AttendedDate']->format('Y-m-d');
            $Remarks = $row['Remarks'];

            // Fetch employee name based on AttendedByUserID
            $empname = "SELECT * FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$AttendedByUserID'" ;
            $resultN = sqlsrv_query($conn, $empname);
            $rowN = sqlsrv_fetch_array($resultN, SQLSRV_FETCH_ASSOC);
            
            if ($rowN !== false) {
                $name = $rowN['emp_name'];
               
                echo '<tr>';
                echo '<td style="padding: 8px;">' . $name . ' (' . $AttendedByUserID . ')</td>';
                echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                echo '</tr>';
            }
        }
    }
} elseif (strtoupper($row['Status']) == "C") {
    $statusC = "SELECT AttendedByUserID, AttendedDate, Remarks FROM [complaint].[dbo].[ComplaintAttendDet] WHERE CompNo = '$compNo'" ;

    // Execute the query
    $resultC = sqlsrv_query($conn, $statusC);
    
    if (sqlsrv_has_rows($resultC)) {
        echo '<tr>';
        echo '<td style="background-color: #5DADE2;">Remarks By</td>';
        echo '<td style="background-color: #5DADE2;">Remarks Date</td>';
        echo '<td style="background-color: #5DADE2;">Remarks</td>';
        echo '</tr>';
    
        while ($rowC = sqlsrv_fetch_array($resultC, SQLSRV_FETCH_ASSOC)) {
            $AttendedByUserID = $rowC['AttendedByUserID'];
            $AttendedDate = $rowC['AttendedDate']->format('Y-m-d');
            $Remarks = $rowC['Remarks'];
            //print 'AttendedByUserID' .$AttendedByUserID;
            
            
            // Fetch employee name based on AttendedByUserID
            $empname = "SELECT emp_name FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$AttendedByUserID'";
            $resultN = sqlsrv_query($conn, $empname);
            
            if ($resultN !== false) {
                $rowN = sqlsrv_fetch_array($resultN, SQLSRV_FETCH_ASSOC);
            
                if (!empty($rowN['emp_name'])) {
                    $name = $rowN['emp_name'];
                    echo '<tr>';
                    echo '<td style="padding: 8px;">' . $name . ' (' . $AttendedByUserID . ')</td>';
                    echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                    echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                    echo '</tr>';
                } else {
                    // Name is empty or null, display a row with empty cells
                    echo '<tr>';
                    echo '<td style="padding: 8px;"></td>';
                    echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                    echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                    echo '</tr>';
                }
            } else {
                // Query execution failed, display a row with empty cells
                echo '<tr>';
                echo '<td style="padding: 8px;"></td>';
                echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                echo '</tr>';
            }
            
        }
    }
}



echo '</table>';
echo '</div>';
echo '</center>';



   

} 
//else {
//     echo "<p>No details found for complaint number: " . $compNo . "</p>";
// }

// Clean up resources
sqlsrv_free_stmt($result);
echo '</body>'
?>
 <?php
    // Retrieve the complaint number from the URL parameter
    $compNo = $_GET['compNo'];

    // Array of allowed image extensions
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

    // Flag to indicate if a valid image was found
    $imageFound = false;

    // Loop through the allowed extensions and check if the image file exists
    foreach ($allowedExtensions as $extension) {
        $imageURL = '../images/' . $compNo . '.' . $extension;
        if (file_exists($imageURL)) {
            // Display the image
            echo '<img src="' . $imageURL . '" alt="Complaint Image" width="100%" height="auto">';
            $imageFound = true;
            break; // Display the first valid image found
        }
    }

    // Display message if no valid image found
    // if (!$imageFound) {
    //     echo 'Image not found or unsupported format.';
    // }
    ?>

<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Mukta&family=Prompt&family=Roboto&family=Signika+Negative:wght@500&display=swap"
        rel="stylesheet">
    <style>
    /* Center align the table */
    table {
        margin: 0 auto;
    }

    /* Add some padding and borders to table cells */
    table td {
        padding: 5px;
        border: 1px solid #ccc;
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
     "Database" => "Complaint",
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
$sql  = "select Plant , emp_name ,emp_num from [Complaint].[dbo].[EA_webuser_tstpp] where emp_num='$_SESSION[emp_num]'";

$params = array($emp_no);

// Prepare and execute the query
$stmt = sqlsrv_query($conn, $sql, $params);

// Check if the query was successful and if there are any matching records
if ($stmt && sqlsrv_has_rows($stmt)) {
    // Fetch the Plant value from the result set
    $row1 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $plant = $row1['Plant'];
    $emp_name =$row1['emp_name'];
    $emp_num =$row1['emp_num'];

    // Print the Plant value
    //echo "plant: " . $plant;
    //echo "emp_name: " . $emp_name;
} else {
    // Handle the case when no matching records are found or any other error
    echo "No records found or an error occurred.";
}

// Retrieve the compNo from the query string parameter
$compNo = $_GET['compNo'];
//$status= $_GET['status'];
//print "complaint no : " . $compNo;

// $strSelComp = "SELECT CT.CompNo AS CompNo, CT.[Time] AS CompTime, CT.CompDate AS compDate, CT.CompOriginDeptID AS compOriginDeptID, CT.CompDeptID AS compDeptID, CT.compTypeID AS compTypeID,
//         CT.Description AS Description, CT.Location AS Location, CT.ContactNo AS ContactNo, CT.CompUserID AS compUserID, CT.Status AS Status, CTM.deptID AS deptID, CTM.compTypeDesc AS compTypeDesc,
//         CTM.deptDesc AS deptDesc, dbo.EA_webuser_tstpp.emp_name  
//         FROM dbo.ComplaintTable CT
//         INNER JOIN dbo.compTypeMas CTM ON CT.compTypeID = CTM.compTypeId AND CT.CompDeptID = CTM.deptID
//         INNER JOIN dbo.EA_webuser_tstpp ON CT.CompUserID = dbo.EA_webuser_tstpp.emp_num
//         WHERE CT.CompNo = " . $compNo;

$strSelComp = "SELECT CT.CompNo AS CompNo, CT.[Time] AS CompTime, CT.CompDate AS compDate, CT.CompOriginDeptID AS compOriginDeptID, CT.CompDeptID AS compDeptID, CT.compTypeID AS compTypeID,
        CT.Description AS Description, CT.Location AS Location, CT.ContactNo AS ContactNo, CT.CompUserID AS compUserID, CT.Status AS Status, CTM.deptID AS deptID, CTM.compTypeDesc AS compTypeDesc,
        CTM.deptDesc AS deptDesc, dbo.EA_webuser_tstpp.emp_name  
        FROM dbo.ComplaintTable CT
        INNER JOIN dbo.compTypeMas CTM ON CT.compTypeID = CTM.compTypeId AND CT.CompDeptID = CTM.deptID
        INNER JOIN dbo.EA_webuser_tstpp ON RIGHT(CT.CompUserID, 6) = RIGHT(dbo.EA_webuser_tstpp.emp_num, 6)
        WHERE CT.CompNo = " . $compNo;

$result = sqlsrv_query($conn, $strSelComp);

// Check if the query was successful
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (sqlsrv_has_rows($result)) {
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    $dept = $row['compOriginDeptID'];
     

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

<body>
    <div class="container">
        <form name="frmAddRemark" id="frmAddRemark" method="post" action="">
            <input type="hidden" id="frmAction" name="frmAction">
            <table border="0" align="center">
            <?php if (!empty($row)) { ?>
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
                        <table>
                            <tr>
                                <td>
                                    <b><?php echo strtoupper($row["compTypeDesc"]); ?></b>
                                </td>
                                <td>
                                    <?php
                                        // SHOW IF COMPLAINTS ARE NOT ATTENDED
                                        if (strtoupper(trim($row["Status"])) != "C") {
                                            $query = "SELECT compTypeID, deptID, compTypeDesc, Plant FROM compTypeMas where deptID= $row[deptID]";
                                            $stmt = sqlsrv_query($conn, $query);

                                            // Check if the query was successful
                                            if ($stmt !== false) {
                                                echo '<select name="complaintType">';
                                                echo '<option value="new">New Complaint Type</option>'; // New option
                                                while ($typeRow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    $compTypeID = $typeRow['compTypeID'];
                                                    $deptID = $typeRow['deptID'];
                                                    $compTypeDesc = $typeRow['compTypeDesc'];

                                                    // Output an option element for each complaint type
                                                    echo '<option value="' . $compTypeID . '">' . $compTypeDesc . '</option>';
                                                }
                                                echo '</select>';
                                            } else {
                                                // Handle any errors in retrieving data from the database
                                                echo 'Failed to fetch complaint types.';
                                            }
                                            sqlsrv_free_stmt($stmt);
                                        }
                                    ?>
                                </td>
                                <td>
                                    <input type="submit" name="updateComplaintType" value="Update Complaint Type">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                if (isset($_POST['updateComplaintType'])) {
                    $selectedCompTypeID = $_POST['complaintType'];

                    //echo $selectedCompTypeID;

                    // Update the compTypeID in the ComplaintTable
                    $updateQuery = "UPDATE ComplaintTable SET compTypeID = '$selectedCompTypeID' WHERE compNo = '$compNo'";
                    $updateResult = sqlsrv_query($conn, $updateQuery);

                    if ($updateResult !== false) {
                        echo '<script>alert("Complaint Type updated successfully.");</script>';

                        //header('Location: attendcomplaint.php');
                    } else {
                        echo 'Failed to update the complaint type.';
                    }
                }
                ?>
                <tr>
                    <td><b>Location</b></td>
                    <td valign="top">:&nbsp;</td>
                    <td><?php echo $row["Location"]; ?></td>
                </tr>
                <tr>
                    <td><b>Contact Number</b></td>
                    <td valign="top">:&nbsp;</td>
                    <td><?php echo strtoupper($row["ContactNo"]); ?></td>
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
                    echo "Complaint is <b style='color:#FF9900;'>Remarked</b>.";
                }
            ?></td>
                    <!----------------------------------Complaint Attending Details :----------------------------------------->
                <tr>
                    <td colspan="3">
                        <h3 align="center">
                            <u>Complaint Attending Details :-</u>
                        </h3>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><b>Complaint Attend Date</b></td>
                    <td valign="top">:&nbsp;</td>
                    <td id="currentDateTime" style="font-size:30px;background-color:black;">
                    </td>
                </tr>
                <style>
                @font-face {
                    font-family: "DS-DIGIT";
                    src: url("DS-DIGIT.ttf");
                }

                #currentDateTime {
                    font-family: "DS-DIGIT";
                }
                </style>
                <script>
                function getCurrentDateTime() {
                    var currentDateTime = new Date();
                    var year = currentDateTime.getFullYear();
                    var month = ("0" + (currentDateTime.getMonth() + 1)).slice(-2); // Add leading zero if needed
                    var day = ("0" + currentDateTime.getDate()).slice(-2); // Add leading zero if needed
                    var hours = currentDateTime.getHours();
                    var minutes = ("0" + currentDateTime.getMinutes()).slice(-2); // Add leading zero if needed
                    var seconds = ("0" + currentDateTime.getSeconds()).slice(-2); // Add leading zero if needed
                    var ampm = hours >= 12 ? "PM" : "AM";

                    hours = hours % 12;
                    hours = hours ? hours : 12;

                    var date = "<span style='color: #EEB7AD;'>" + year + "-" + month + "-" + day + "</span>";
                    var time = hours + ":" + minutes + ":" + seconds + " " + ampm;
                    document.getElementById("currentDateTime").innerHTML = date + " ";
                    document.getElementById("currentDateTime").innerHTML +=
                        "<span id='time' style='color: #6191DF;'>&nbsp;&nbsp;[&nbsp;" +
                        time + "&nbsp;]</span>";
                    setTimeout(getCurrentDateTime, 1000);
                }

                // Call the function to start updating the date and time
                getCurrentDateTime();


                // Set the font of the time element
                var digitalClockFont = "DS-DIGIT";
                document.getElementById("currentDateTime").style.fontFamily = digitalClockFont;
                </script>

                <tr>
                    <td valign="top"><b>Complaint Attend By</b></td>
                    <td valign="top">:&nbsp;</td>
                    <td><?php echo $emp_name; ?>
                        <input type="hidden" name="compUserID" value="<?php echo $row["compUserID"]; ?>">
                    </td>
                </tr>
                <tr>
    <td valign="top"><b>Remarks/Action Taken</b></td>
    <td valign="top">:&nbsp;</td>
    <td><textarea id="remarksTextarea" name="remarks"></textarea></td>
</tr>

<tr>
    <td valign="top"><b>Whether complaint is Completed.</b></td>
    <td valign="top">:&nbsp;</td>
    <td>
        <label>
            <input type="radio" id="attendedFlagC" name="attendedFlag" value="C"
                onclick="updateRemarksTextarea('Completed')"> Yes
        </label>
        <label>
            <input type="radio" id="attendedFlagR" name="attendedFlag" value="R"
                onclick="updateRemarksTextarea('Remarked')"> No
        </label>
    </td>
</tr>

<!-- <script>
var remarksTextarea = document.getElementById('remarksTextarea');
var remarksContent = ''; // Variable to store the remarks content

function updateRemarksTextarea(value) {
    var selectedValue = document.querySelector('input[name="attendedFlag"]:checked').value;
    if (selectedValue === 'C' || selectedValue === 'R') {
        remarksContent = remarksTextarea.value; // Get the current remarks content
        remarksContent = remarksContent.replace(/\b(?:completed|remarked)\b/gi, ''); // Remove existing completed or remarked
        remarksContent = remarksContent.trim() + ' ' + value; // Append the selected option
        remarksTextarea.value = remarksContent.trim();
    }
}
</script> -->






                <script>
                function updateRadioValue(value) {
                    var attendedFlagC = document.getElementById('attendedFlagC');
                    var attendedFlagR = document.getElementById('attendedFlagR');

                    if (value === 'C') {
                        attendedFlagC.checked = true;
                        attendedFlagR.checked = false;
                    } else if (value === 'R') {
                        attendedFlagC.checked = false;
                        attendedFlagR.checked = true;
                    }
                }

                function submitForm() {
                    var attendedFlagC = document.getElementById('attendedFlagC');
                    var attendedFlagR = document.getElementById('attendedFlagR');

                    if (!attendedFlagC.checked && !attendedFlagR.checked) {
                        alert("Please select whether the complaint is Completed/Attended.");
                        return false;
                    }
                }
                </script>

                <tr>
                    <td colspan="3">
                        <button type="submit" name="complaintAttend" class="btn btn-primary"
                            onclick="return submitForm()">Submit</button>
                    </td>
                </tr>

        
               
    </table>
</form>
        <?php
    if (isset($_POST['complaintAttend'])) {
    $compNo = $_GET['compNo'];
    $remarks = $_POST['remarks'];
    $attendedFlag = $_POST['attendedFlag'];
    $currentDateTime = date('Y-m-d');
    $currentTime = date('H:i:s');
    //$compUserID = $_POST['compUserID']; // Set the compUserID value based on your requirement
    $emp_no = $_SESSION["emp_num"];
    $status = "";

    // Determine the status based on the selected radio button
    if ($attendedFlag == 'C') {
        $status = 'C'; // Completed
    } elseif ($attendedFlag == 'R') {
        $status = 'R'; // Remarked
    }

    //print $emp_no;
    // Check the existing status in ComplaintTable
    $checkStatusQuery = "SELECT Status FROM ComplaintTable WHERE CompNo = '$compNo'";
    $checkStatusResult = sqlsrv_query($conn, $checkStatusQuery);

    if ($checkStatusResult !== false) {
        $statusRow = sqlsrv_fetch_array($checkStatusResult, SQLSRV_FETCH_ASSOC);
        $currentStatus = strtoupper($statusRow['Status']);

        // Perform different actions based on the status
        if ($currentStatus == "N") {
            // Insert a new record
            $updateQuery = "UPDATE ComplaintAttendDet SET Remarks = '$remarks', AttendedDate = '$currentDateTime', AttendedByUserID = '$emp_no', 
            TimeStamp = '$currentDateTime', Time = '$currentTime' WHERE compNo = '$compNo'";
        
            $updateResult = sqlsrv_query($conn, $updateQuery);
        
            if ($updateResult !== false) {
                echo 'Complaint details updated successfully.';
        
                // Update the ComplaintTable status
                $strUpdateQry = "UPDATE ComplaintTable SET Status = '$status' WHERE CompNo = '$compNo'";
                $updateStatusResult = sqlsrv_query($conn, $strUpdateQry);
        
                if ($updateStatusResult !== false) {
                    echo '<script>alert(Complaint status updated successfully.");</script>';
                    //echo '<script>window.location.href = "attendComplaint.php";</script>';
                    //header('Location: attendcomplaint.php');
                    exit;
                } else {
                    echo '<script>alert(Failed to update the complaint status.");</script>';
                }
            }         
        } elseif ($currentStatus == "R") {
            // Update the existing record
            $updateQuery = "INSERT INTO ComplaintAttendDet (CompNo, Remarks, AttendedDate, AttendedByUserID, TimeStamp, Time) VALUES ('$compNo', '$remarks', '$currentDateTime', '$emp_no', '$currentDateTime', '$currentTime')";
            $updateResult = sqlsrv_query($conn, $updateQuery);

            if ($updateResult !== false) {
                echo 'Complaint details updated successfully.';

                // Update the ComplaintTable status
                $strUpdateQry = "UPDATE ComplaintTable SET Status = '$status' WHERE CompNo = '$compNo'";
                $updateStatusResult = sqlsrv_query($conn, $strUpdateQry);

                if ($updateStatusResult !== false) {
                    echo '<script>alert("Complaint status updated successfully.");</script>';
                    //echo '<script>window.location.href = "attendComplaint.php";</script>';
                    //header('Location: attendcomplaint.php');
                    exit;
                } else {
                    echo '<script>alert("Failed to update the complaint status.");</script>';
                }
            } else {
                echo '<script>alert("Failed to update the complaint details.");</script>';
            }
        }
    } else {
        echo '<script>alert("Failed to retrieve the current status.");</script>';
    }
}}
?>

        <?php

        
    if (strtoupper($row['Status']) == "R") {
        $statusR = "SELECT AttendedByUserID, AttendedDate, Remarks FROM [complaint].[dbo].[ComplaintAttendDet] WHERE CompNo = " . $compNo;

        // Execute the query
        $resultR = sqlsrv_query($conn, $statusR);
        if (sqlsrv_has_rows($resultR)) {
            echo '<table>';
            echo '<tr>';
            echo '<td style="background-color: #58D68D;">Remarks By</td>';
            echo '<td style="background-color: #58D68D;">Remarks Date</td>';
            echo '<td style="background-color: #58D68D;">Remarks</td>';
            echo '</tr>';

            while ($rowR = sqlsrv_fetch_array($resultR, SQLSRV_FETCH_ASSOC)) {
                $AttendedByUserID = $rowR['AttendedByUserID'];
                $AttendedDate = $rowR['AttendedDate']->format('Y-m-d');
                $Remarks = $rowR['Remarks'];
                

                $empname = "SELECT emp_name FROM [complaint].[dbo].[ComplaintAttendDet] JOIN [Complaint].[dbo].[EA_webuser_tstpp] ON ComplaintAttendDet.AttendedByUserID=EA_webuser_tstpp.emp_num WHERE ComplaintAttendDet.CompNo= " . $compNo;
                    $resultN = sqlsrv_query($conn, $empname);
                    $rowN = sqlsrv_fetch_array($resultN, SQLSRV_FETCH_ASSOC);

                    if ($rowN !== false) {
                        $name = $rowN['emp_name'];

                echo '<tr>';
                echo '<td style="padding: 8px;">' . $name . '</td>';
                echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                echo '</tr>';
                
            }
        }
        echo '</table>';
        } elseif (strtoupper($row['Status']) == "C") {
            $statusC = "SELECT AttendedByUserID, AttendedDate, Remarks FROM [complaint].[dbo].[ComplaintAttendDet] WHERE CompNo = " . $compNo;

            // Execute the query
            $resultC = sqlsrv_query($conn, $statusC);

            if (sqlsrv_has_rows($resultC)) {
                echo '<table>';
                echo '<tr>';
                echo '<td style="background-color: #5DADE2;">Remarks By</td>';
                echo '<td style="background-color: #5DADE2;">Remarks Date</td>';
                echo '<td style="background-color: #5DADE2;">Remarks</td>';
                echo '</tr>';

                while ($rowC = sqlsrv_fetch_array($resultC, SQLSRV_FETCH_ASSOC)) {
                    $AttendedByUserID = $rowC['AttendedByUserID'];
                    $AttendedDate = $rowC['AttendedDate']->format('Y-m-d');
                    $Remarks = $rowC['Remarks'];

                    $empname = "SELECT emp_name FROM [complaint].[dbo].[ComplaintAttendDet] JOIN [Complaint].[dbo].[EA_webuser_tstpp] ON ComplaintAttendDet.AttendedByUserID=EA_webuser_tstpp.emp_num WHERE ComplaintAttendDet.CompNo= " . $compNo;
                    $resultN = sqlsrv_query($conn, $empname);
                    $rowN = sqlsrv_fetch_array($resultN, SQLSRV_FETCH_ASSOC);

                    if ($rowN !== false) {
                        $name = $rowN['emp_name'];

                        echo '<tr>';
                        echo '<td style="padding: 8px;">' . $name . '</td>';
                        echo '<td style="padding: 8px;">' . $AttendedDate . '</td>';
                        echo '<td style="padding: 8px;">' . $Remarks . '</td>';
                        echo '</tr>';
                    }
                }
                echo '</table>';
            }
        }
    } 
?>

<?php } else { ?>
            <tr>
                <td colspan="3">No records found.</td>
            </tr>
        <?php } ?>
<br>
        <!-- <p>*Note : After submit close the modal & <span style="background-color:yellow;"><i>REFRESH</i></span>&nbsp;&nbsp;the page
        </p> -->
    </div>
</body>
</html>
<center>
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
    ?></center>
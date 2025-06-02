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


$empno = $_SESSION['emp_num'];
$plantQuery = "select  EA_DeptCode_Mas.Dept_id , DeptName ,EA_webuser_tstpp.Plant from EA_DeptCode_Mas join EA_webuser_tstpp on EA_DeptCode_Mas.Dept_id = EA_webuser_tstpp.dept_code where emp_num = '$empno'";
$plantResult = sqlsrv_query($conn, $plantQuery);

if ($plantResult) {
    $row = sqlsrv_fetch_array($plantResult, SQLSRV_FETCH_ASSOC);
    if ($row !== null) {
        $deptDesc = $row['DeptName'];
        $compDeptID = $row['Dept_id'];
        $plant =$row['Plant'];
    } else {
        $deptDesc = "Unknown Department";
    }
} else {
    $deptDesc = "Unknown Department";
}



//print "depart name :.$deptDesc.";
//print "depart name :.$compDeptID.";
//print "depart name :.$startDate.";
//print "depart name :.$endDate.";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Attended Complaint Report</title>

    <style>
    th {
        background-color: #f0f0f0;
        /* Set your desired header color */
    }

    .scroll-up,
    .scroll-down {
        border-radius: 9px;
        /* Set the desired border radius value */
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    table {
        height: 500px;
        height: 700px;
        overflow: auto;
    }
    .modal {
        display: none;
    position: fixed;
    top: 0;
    left: 50%;
    bottom: 0;
    width: 50%;
    height: 100%;
    background-color: transparent;
    z-index: 9999;
    transform: translateY(100%);
    transition: transform 0.3s ease-out;
    }

    .modal.active {
        transform: translateY(0);
    }

    .modal-content {
        height: 100%;
        width: 100%;
        background-color: #D0ECE7;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 20px;
        position: relative;
    }

    .modal-content button {
       
        cursor: pointer;
        color: #E74C3C;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        background-color: transparent;
        border: none;
    }
    </style>
    <script>
            function openModal(url) {
                var modal = document.getElementById("modal");
                var iframe = document.getElementById("modal-iframe");

                // Set the iframe source to the specified URL
                iframe.src = url;

                // Display the modal
                modal.style.display = "block";

                // Add the active class to trigger the transition
                setTimeout(function() {
                    modal.classList.add("active");
                }, 0);
            }

            function closeModal() {
                var modal = document.getElementById("modal");
                modal.classList.remove("active");
                setTimeout(function() {
                    modal.style.display = "none";
                }, 500);
            }

            document.querySelector(".modal-close").addEventListener("click", closeModal);
            </script>
</head>

<body>
    <center>
        <div class="container">


            <h4><u><?php echo $deptDesc; ?> Department's Attended Complaint Report</u></h4><br>

            <form method="POST">
                <label><i><u>Please Select Date </u>:-</i></label>&nbsp;
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
                <input type="submit" value="Generate Report">
        </form>
                <?php 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startDate = $_POST["start_date"];
    $endDate = $_POST["end_date"];

    // Convert dates to the appropriate format (yyyy-mm-dd)
    $formattedStartDate = date('Y-m-d', strtotime($startDate));
    $formattedEndDate = date('Y-m-d', strtotime($endDate));

    //$compDeptID = '8000'; // Assuming the department ID is fixed to '35'
    // $strSelComp = "SELECT CA.Timestamp as AttendDate, CA.[Time] as AttendTime, CT.CompNo as CompNo, CT.TimeStamp as TimeStamp, CT.CompDate as CompDate, CA.AttendedByUserID as AttendedByUserID, CT.compTypeID as compTypeID, CT.Status as Status, " .
    // " CTM.compTypeDesc as compTypeDesc, CTM.deptDesc as DeptDesc, EWT.emp_name as emp_name, CT.CompOriginDeptID AS compOriginDeptID " .
    // " FROM ComplaintTable CT, compTypeMas CTM, ComplaintAttendDet CA, EA_webuser_tstpp EWT" .
    // " WHERE CT.compTypeID = CTM.compTypeId AND " .
    // " CT.CompDeptID = '$compDeptID' AND " .
    // " CT.CompNo = CA.CompNo AND " .
    // " CT.Status = 'C' " .
    // " and EWT.emp_num = CA.AttendedByUserID " .
    // " AND CT.CompDate >= '$formattedStartDate' AND CT.CompDate <= '$formattedEndDate'" .
    // " ORDER BY CompDate DESC, compNo DESC";

    // $strSelComp="SELECT CA.Timestamp as AttendDate, CA.[Time] as AttendTime, CT.CompNo as CompNo, CT.TimeStamp as TimeStamp, CT.CompDate as CompDate, CA.AttendedByUserID as AttendedByUserID, CT.compTypeID as compTypeID, CT.Status as Status, 
    // CTM.compTypeDesc as compTypeDesc, CTM.deptDesc as DeptDesc, EWT.emp_name as emp_name, CT.CompOriginDeptID AS compOriginDeptID,
    // (SELECT DeptName FROM EA_DeptCode_Mas WHERE Dept_id = CT.CompOriginDeptID) AS DeptName
    // FROM ComplaintTable CT, compTypeMas CTM, ComplaintAttendDet CA, EA_webuser_tstpp EWT
    // WHERE CT.compTypeID = CTM.compTypeId AND 
    // CT.CompDeptID = '$compDeptID' AND 
    // CT.CompNo = CA.CompNo AND 
    // CT.Status = 'C' AND
    // EWT.emp_num = CA.AttendedByUserID AND
    // CT.CompDate >= '$formattedStartDate' AND CT.CompDate <= '$formattedEndDate'
    // ORDER BY CompDate DESC, compNo DESC
    // ";

    $strSelComp="SELECT CT.CompNo as CompNo, CA.Timestamp as AttendDate, MAX(CA.[Time]) as AttendTime, CT.TimeStamp as TimeStamp, CT.CompDate as CompDate, CA.AttendedByUserID as AttendedByUserID, CT.compTypeID as compTypeID, CT.Status as Status, 
    CTM.compTypeDesc as compTypeDesc, CTM.deptDesc as DeptDesc, EWT.emp_name as emp_name, CT.CompOriginDeptID AS compOriginDeptID, MAX(CA.[Time]) as LastUpdatedTime,
    (SELECT DeptName FROM EA_DeptCode_Mas WHERE Dept_id = CT.CompOriginDeptID) AS DeptName
FROM ComplaintTable CT
JOIN compTypeMas CTM ON CT.compTypeID = CTM.compTypeId
JOIN (
    SELECT CompNo, MAX([Time]) as [Time]
    FROM ComplaintAttendDet
    GROUP BY CompNo
) CA_MaxTime ON CT.CompNo = CA_MaxTime.CompNo
JOIN ComplaintAttendDet CA ON CT.CompNo = CA.CompNo AND CA.[Time] = CA_MaxTime.[Time]
JOIN EA_webuser_tstpp EWT ON EWT.emp_num = CA.AttendedByUserID
WHERE CT.CompDeptID = '$compDeptID' AND 
    CT.Status = 'C' AND 
    CT.CompDate >= '$formattedStartDate' AND CT.CompDate <= '$formattedEndDate'
GROUP BY CT.CompNo, CA.Timestamp, CT.TimeStamp, CT.CompDate, CA.AttendedByUserID, CT.compTypeID, CT.Status, CTM.compTypeDesc, CTM.deptDesc, EWT.emp_name, CT.CompOriginDeptID
ORDER BY CompDate DESC, compNo DESC";

    $result = sqlsrv_query($conn, $strSelComp);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $dataRows = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $dataRows[] = $row;
    }
}
?>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
    <?php echo '<span style="text-decoration: underline;">Reports date</span>&nbsp;: &nbsp; <span style="font-style: italic; background-color: yellow;">' . $startDate . '</span> from <span style="font-style: italic; background-color: yellow;">' . $endDate . '</span>'; ?>

    <?php if (empty($dataRows)) { ?>
        <p>No records found.</p>
    <?php } else { ?>
        <div style="height: 650px; overflow: auto;"><br>
            <table class="table table-striped" align="center" border="1.5" cellspacing="0">
                <thead style="position: sticky; top: 0; background-color: #c3e6cb;">
                    <tr align="center">
                        <th style="padding: 8px;">S.No.</th>
                        <th style="padding: 8px;">Comp.No.</th>
                        <th style="padding: 8px;">Comp.Dept.</th>
                        <th style="padding: 8px;">Complaint Attend By</th>
                        <th style="padding: 8px;">Complaint Type</th>
                        <th style="padding: 8px;">Comp.Date</th>
                        <th style="padding: 8px;">Attend.Date</th>
                        <th style="padding: 8px;">Comp.Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rowNum = 1;
                    foreach ($dataRows as $row) { ?>
                        <tr align="center">
                            <td>
                                
                            <?php echo $rowNum; ?>
                            
                        </td>
                            <td>
                            <a href="#" onclick="openModal('../complaint_reg/complaintType.php?compNo=<?php echo $row['CompNo']; ?>')"
                                    title="Click here to see details of complaint">
                                <?php echo $row["CompNo"]; ?>
                                </a>
                        </td>
                            <td><?php echo $row["DeptName"]; ?></td>
                            <td><?php echo $row["emp_name"]; ?></td>
                            <td>
                                
                                    <?php echo $row['compTypeDesc']; ?>
                                
                            </td>
                            <td>
                                <font color="blue">[<?php echo $row["TimeStamp"]->format('Y-m-d H:i:s'); ?>]</font>
                            </td>
                            <td>
                                <font color="red">[<?php echo $row["AttendDate"]->format('Y-m-d') . ' ' . $row["AttendTime"]; ?>]</font>
                            </td>
                            <td><?php echo $row["Status"]; ?></td>
                        </tr>
                        <?php
                        $rowNum++;
                    } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
<?php } ?>

           
            
 <!-- Modal -->
 <center>
                <div id="modal" class="modal">
                    <div class="modal-content">
                        <h2></h2>
                        <iframe id="modal-iframe" frameborder="0" width="100%" height="100%"></iframe>
                        <button class="modal-close" onclick="closeModal()">
                            <img src="images/icons8-close.gif" alt="Close">&nbsp;&nbsp;CLOSE
                        </button>
                    </div>
                </div>
            </center>
            
    </center>
</body>

</html>
<?php include 'footer.php';?>
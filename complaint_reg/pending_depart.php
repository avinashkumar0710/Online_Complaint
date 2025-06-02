<?php include 'Complaintheader.php';?>
<?php 
//database connection
session_start();
$serverName = "192.168.100.240";
$connectionInfo = array(
    "Database" => "complaint",
     "UID" => "sa",
    "PWD" => "Intranet@123"
);           
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Pending Complaints for your Department</title>
    <style>
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
</head>

<body>
    <center>
        
        <div class="container">
        <?php
    // Assuming you have established a database connection $conn
    $userId = $_SESSION['emp_num'];
    //print $userId;
    $findept = "select dept_code ,DeptName from EA_webuser_tstpp join EA_DeptCode_Mas on EA_webuser_tstpp.dept_code=EA_DeptCode_Mas.Dept_id where emp_num='$userId'";
    $result = sqlsrv_query($conn, $findept);
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    $dept_code = $row['dept_code'];
    $DeptName =$row['DeptName'];
    //print $dept_code;
    //print $DeptName;

    $strSelComp = "SELECT DISTINCT CT.CompNo AS CompNo, EMS.loc_desc AS Plant, CT.CompDate AS CompDate, CT.TimeStamp as TimeStamp,CT.Time AS CompTime, CT.CompDeptID AS CompDeptID, CT.compTypeID AS compTypeID, CT.Status AS Status, " .
        "CTM.compTypeDesc AS compTypeDesc, CTM.deptDesc AS DeptDesc, CT.CompOriginDeptID AS compOriginDeptID " .
        "FROM ComplaintTable CT " .
        "INNER JOIN dbo.compTypeMas CTM ON CT.compTypeID = CTM.compTypeId AND CT.CompDeptID = CTM.deptID " .
        "JOIN emp_mas_sap EMS ON CT.Plant = EMS.location " .
        "WHERE CT.CompDeptID = '" .  $dept_code . "' " .
        "AND CT.Status != 'C' " .
        "ORDER BY CompDate DESC, compNo DESC";

    $result = sqlsrv_query($conn, $strSelComp);
    
    
    // Check if the query was successful
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (!sqlsrv_has_rows($result)) {
        //echo "<script>console.log('No department found for complaint number " . $row['CompNo'] . "');</script>";
    }
    
    
    if (sqlsrv_has_rows($result)) {
       

    echo '<style>';
    echo 'table { border-collapse: collapse; }';
    echo 'th, td { border: 1px solid #ccc; padding: 8px; }';
    echo 'th { background-color: #f2f2f2; font-weight: bold; line-height: 1.5; }';
    echo 'tr { line-height: 1.2; }';
    echo '</style>';

    //echo '<p align="center" style="color: #black;"><u><i>* After submit please <span style="background-color:yellow">Refresh</span> the page. *</i></u></p>';
    echo '<h3 align="center"><u>Check Pending Complaints for your Department ' . $DeptName . ' </u></h3>';
    echo '<div style="height: 650px; overflow: auto;">';
    echo '<table align="center" width="90%" border="1" cellspacing="0" style="border-collapse: collapse; border: 1px solid #ccc;">';
    echo '<tr style="background-color: #f2f2f2; font-weight: bold;">';
    echo '<th style="padding: 8px;">Comp.No.</th>';
    echo '<th style="padding: 8px;">Plant</th>';
    echo '<th style="padding: 8px;">Complaint Department</th>';
    echo '<th style="padding: 8px;">Complaint Type</th>';
    echo '<th style="padding: 8px;">Complaint Date</th>';
    echo '<th style="padding: 8px;">Complaint Status</th>';
    echo '</tr>';
    $data = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $dept = $row['compOriginDeptID'];

        $findDeprt = "SELECT Dept_id, DeptName 
                      FROM EA_DeptCode_Mas 
                      WHERE Dept_id = '$dept' ";
        $result1 = sqlsrv_query($conn, $findDeprt);

        if ($result1 === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (!sqlsrv_has_rows($result1)) {
            echo "<script>console.log('No department found for complaint number " . $row['CompNo'] . "');</script>";
        }
        

        if (sqlsrv_has_rows($result1)) {
            $row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC);
            $deptNameForRow = $row1['DeptName'];

            echo '<tr>';
            echo '<td style="padding: 8px;"><a href="#" onclick="openModal(\'../complaint_reg/attendComplaint.php?compNo=' . $row['CompNo'] . '\')" title="Click here to see details of complaint">' . $row['CompNo'] . '</a></td>';
            echo '<td style="padding: 8px;">' . $row['Plant'] . '</td>';
            echo '<td style="padding: 8px;">' .  $row1['DeptName']    . '</td>';
            echo '<td style="padding: 8px;">' . $row['compTypeDesc'] . '</td>';
            $timestamp = "2023-05-29T14:06:16+02:00"; // Replace with your timestamp variable
            $unixTimestamp = strtotime($timestamp);
            $formattedTime = date('H:i:s', $unixTimestamp); // Format the time portion
            echo '<td style="padding: 8px;">' . $row['CompDate']->format('Y-m-d') .'&nbsp;&nbsp;<font color="blue">['.$row['CompTime'].']</td>';
            echo '<td style="padding: 8px;"><b>' . $row['Status'] . '</b></td>';
            echo '</tr>';
        }
        $data[] = $row;
    }
    echo "<script>console.log('PHP Data:', " . json_encode($data) . ");</script>";
   }
   else{
    echo "<br><tr><td colspan='7'><h4>No pending complaints.</h4></td></tr>";
   }

    echo '</table>';
    echo '</div>';
    echo '<p style="text-align: center; margin-top: 20px; font-weight: bold; padding: 10px; background-color: #D0ECE7; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-radius: 5px;">Complaint Status Legend: <span style="color: #FF0000;">N</span> - Newly Registered Complaint, <span style="color: #FF9900;">R</span> - Maintenance department Seen and Remarked the complaint, <span style="color: #008000;">C</span> - Complaint is closed</p>';

?>


        </div>
       

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
            // Refresh the parent page (pending_depart.php)
            window.location.href = "pending_depart.php";
        }, 300);
    }

    document.querySelector(".modal-close").addEventListener("click", closeModal);
</script>

    </center>
</body>

</html>
<?php include 'footer.php';?>
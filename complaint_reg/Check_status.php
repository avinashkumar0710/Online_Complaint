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
    <title>Complaint Register Status</title>

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

            echo '<style>';
            echo 'table { border-collapse: collapse; }';
            echo 'th, td { border: 1px solid #ccc; padding: 8px; }';
            echo 'th { background-color: #f2f2f2; font-weight: bold; line-height: 1.5; }';
            echo 'tr { line-height: 1.2; }';
            echo '</style>';

            echo '<h3 align="center"><u>Complaints Registered By You</u></h3>';
            echo '<div style="height: 700px; overflow: auto;">';
            echo '<table align="center" width="90%" border="1" cellspacing="0" style="border-collapse: collapse; border: 1px solid #ccc;">';
            echo '<tr style="background-color: #f2f2f2; font-weight: bold;">';
            echo '<th style="padding: 8px;">Comp.No.</th>';
            echo '<th style="padding: 8px;">Comp.Dept.</th>';
            echo '<th style="padding: 8px;">Complaint Type</th>';
            echo '<th style="padding: 8px;">Complaint Desc</th>';
            echo '<th style="padding: 8px;">Location</th>';
            echo '<th style="padding: 8px;">Comp.Date</th>';
            echo '<th style="padding: 8px;">Comp.Status</th>';
            echo '</tr>';   

            // Prepare the SQL query
            $strSelComp = "SELECT CT.CompNo as CompNo, CT.Time as CompTime, CT.TimeStamp as TimeStamp, CT.CompDate as CompDate, CT.CompDeptID as CompDeptID, CT.compTypeID as compTypeID, CT.Status as Status, 
                CTM.compTypeDesc as compTypeDesc, CTM.deptDesc as deptDesc, CT.description as Description, CT.location as Location, CTM.deptDesc as DeptDesc  
                FROM ComplaintTable CT, compTypeMas CTM
                WHERE CT.compTypeID = CTM.compTypeId AND CT.CompUserID = '$userId' ORDER BY CompDate DESC, compNo DESC";

            // Execute the query
            $result = sqlsrv_query($conn, $strSelComp);

            // Check if the query was successful
            if ($result === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // Check if there are any rows returned
            if (sqlsrv_has_rows($result)) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $compNo = $row['CompNo'];
                    $compDept = $row['deptDesc'];
                    $compTypeDesc = $row['compTypeDesc'];
                    $compDesc = $row['Description'];
                    $location = $row['Location'];
                    $TimeStamp = $row['TimeStamp'];
                    $compStatus = $row['Status'];

                    echo '<tr>';
                    echo '<td style="padding: 8px;"><a href="#" onclick="openModal(\'../complaint_reg/showCompDet.php?compNo=' . $compNo . '\')" title="Click here to see details of complaint">' . $compNo . '</a></td>';
                    echo '<td style="padding: 8px;">' . $compDept . '</td>';
                    echo '<td style="padding: 8px;">' . $compTypeDesc . '</td>';
                    echo '<td style="padding: 8px;">' . $compDesc . '</td>';
                    echo '<td style="padding: 8px;">' . $location . '</td>';
                    $timestamp = "2023-05-29T14:06:16+02:00"; // Replace with your timestamp variable
                    $unixTimestamp = strtotime($timestamp);
                    $formattedTime = date('H:i:s', $unixTimestamp); // Format the time portion
                    echo '<td style="padding: 8px;color: blue;">' . $TimeStamp->format('Y-m-d [H:i:s]') . '<span style="color: blue;"></span></td>';
                    echo '<td style="padding: 8px;">' . $compStatus . '</td>';
                    echo '</tr>';
                }
            } else {
                // No rows returned
                echo "<tr><td colspan='7'>No complaints registered by you.</td></tr>";
            }

            echo '</table>';
            echo '</div>';
            echo '<p style="text-align: center; margin-top: 20px; font-weight: bold; padding: 10px; background-color: #D0ECE7; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-radius: 5px;">Complaint Status Legend: <span style="color: #FF0000;">N</span> - Newly Registered Complaint, <span style="color: #FF9900;">R</span> - Maintenance department Seen and Remarked the complaint, <span style="color: #008000;">C</span> - Complaint is closed</p>';

            // Clean up resources
            sqlsrv_free_stmt($result);
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
        }, 500);
    }

    document.querySelector(".modal-close").addEventListener("click", closeModal);
</script>
    </center>  
</body>   
</html>
<?php include 'footer.php';?>
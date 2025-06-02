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
//only for testing purpose from here 
$emp_num = $_SESSION['emp_num'];
$sql  = "select Plant from [Complaint].[dbo].[EA_webuser_tstpp] where emp_num='$_SESSION[emp_num]'";

$params = array($emp_num);

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
//only fo testing purpose to here
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
</head>

<body>
    <center>
    <div class="container"><br>


<form method="POST" >
<label><i><u>Please Select Department </u>:&nbsp;&nbsp;</i></label><select name="department" aria-placeholder="Please Select Department">
        <option value="">Select Department</option> <!-- Add this line for the default option -->
        <?php
        // Assuming you have a database connection established using sqlsrv_connect

        $query = "SELECT DISTINCT deptDesc, deptId FROM compTypeMas where plant='$plant'";
        $result = sqlsrv_query($conn, $query);

        // Check if the query was successful
        if ($result) {
            // Fetch each row from the result set
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $deptDesc = $row['deptDesc'];
                $deptId = $row['deptDesc'];
                // Check if the current option matches the selected department
                if (isset($_POST["department"]) && $_POST["department"] == $deptDesc) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo '<option value="' . $deptId . '" ' . $selected . '>' . $deptDesc . '</option>';
            }
        } else {
            // Handle the case when the query fails
            echo 'Error executing the query: ' . sqlsrv_errors();
        }

        // Close the database connection
        //sqlsrv_close($conn);
        ?>  
    </select>
    <input type="submit" value="Generate Report">
</form>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the department is selected
    if (!empty($_POST["department"])) {
        $selectedDepartment = $_POST["department"];

        echo "<u><b>Registered Pending Complaints of " . $selectedDepartment . " Department<br></b></u>";

        // Assuming you have a database connection established using sqlsrv_connect

        // Build the query based on the selected department
        $query1 = "SELECT DISTINCT CT.CompNo as CompNo, CT.Time as CompTime, CT.CompDate as CompDate, CT.Description as CompDesc, CT.CompDeptID as CompDeptID, CT.compTypeID as compTypeID, CT.Status as Status, CTM.compTypeDesc as compTypeDesc, CTM.deptDesc as DeptDesc
              FROM ComplaintTable CT, compTypeMas CTM, EA_webuser_tstpp WU
              WHERE CT.compTypeID = CTM.compTypeId
              AND CTM.deptDesc = '" . $selectedDepartment . "'
              AND CT.CompOriginDeptID = WU.dept_code
              AND CT.Status <> 'C'
              and CT.Plant= '$plant'
              ORDER BY CompDate DESC, compNo DESC";

        $result1 = sqlsrv_query($conn, $query1);
        //echo $result;

        // Check if the query was successful
        if ($result1) {
            echo '<div style="height: 650px; overflow: auto;">';
            echo '<table class="table table-striped" align="center" border="1.5" cellspacing="0">';
            echo '<thead style="position: sticky; top: 0; background-color: #c3e6cb;">';
            echo '<tr align="center">';
            echo '<th>Comp.No.</th>';
            echo '<th>Comp.Dept.</th>';
            echo '<th>Complaint Type</th>';
            echo '<th>Complaint Description</th>';
            echo '<th>Comp.Date</th>';
            echo '<th>Comp.Status</th>';
            
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            // Fetch each row from the result set
            while ($row = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
                echo '<tr align="center">';
                echo '<td><a href="#" onclick="openModal(\'../complaint_reg/others_depart_details.php?compNo=' . $row['CompNo'] . '\')" title="Click here to see details of complaint">' . $row['CompNo'] . '</a></td>';               
                echo '<td>' . $selectedDepartment . '</td>';
                echo '<td>' . $row['compTypeDesc'] . '</td>';
                echo '<td>' . $row['CompDesc'] . '</td>';
                echo '<td> <font color="red">[' . date_format($row['CompDate'], 'Y-m-d') . ']<font color="blue">[Time:' . $row['CompTime'] . ' ]</font></td>';
                echo '<td>' . $row['Status'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
           
            echo '</table>';
        } else {
            // Handle the case when the query fails
            echo 'Error executing the query: ' . sqlsrv_errors();
        }

        // Close the database connection
        sqlsrv_close($conn);
    } else {
        // Handle the case when no department is selected
        echo 'Please select a department.';
    }
}


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
        <p style="text-align: center; margin-top: 20px; font-weight: bold; padding: 10px; background-color: #D0ECE7; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-radius: 5px;">Complaint Status Legend: <span style="color: #FF0000;">N</span> - Newly Registered Complaint, <span style="color: #FF9900;">R</span> - Maintenance department Seen and Remarked the complaint, <span style="color: #008000;">C</span> - Complaint is closed</p>
    </center>
 
</body>
</html>

<?php include 'footer.php';?>
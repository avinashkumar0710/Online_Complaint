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

// Find plant & Department code
$plant_dptcode = "SELECT plant, dept_code FROM EA_webuser_tstpp WHERE emp_num = '$userId'";
$result1 = sqlsrv_query($conn, $plant_dptcode);

if ($result1) {
    while ($row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
        $plant = $row1['plant'];
        $dept_code = $row1['dept_code'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Department Wise Complaint Summary Report</title>
    <style>
        th {
            background-color: #f0f0f0;
        }

        .scroll-up,
        .scroll-down {
            border-radius: 9px;
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <center>
        <div class="container">

        
            <form method="POST" id="myForm">
                <div class="container_background">
                    

                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>

                    <input type="submit" class="btn btn-info" value="Generate Report">
                </div>
            </form>

            <?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $startDate = $_POST["start_date"];
                $endDate = $_POST["end_date"];
                ?>

                <span style="text-decoration: underline;">(Only for Bhilai Location of IT department)&nbsp;&nbsp;Reports date</span>&nbsp;: &nbsp; <span
                    style="font-style: italic; background-color: yellow;"><?php echo $startDate; ?></span> from <span
                    style="font-style: italic; background-color: yellow;"><?php echo $endDate; ?></span>

                <?php
                $noRecords = false;

                if ($noRecords) { ?>

                <?php } else { ?>
                    <br><br>
                    <div style="height: 600px; overflow: auto;">
                        <table class="table table-striped" align="center" border="1.5" cellspacing="0">
                            <thead style="position: sticky; top: 0; background-color: #c3e6cb;">
                                <tr align="center">
                                    <th style="padding: 8px;">Complaint Type</th>
                                    <th style="padding: 8px;">Total No. of Complaints</th>
                                    <th style="padding: 8px;">Attended</th>
                                    <th style="padding: 8px;">Pending</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //$selectedDepartment = $_POST['selectDepartment'];
                                //$selectedPlant = isset($_POST['selectPlant']) ? $_POST['selectPlant'] : $_POST['hiddenPlant']; 

                                $datefind = "SELECT DISTINCT compTypeId, deptID, compTypeDesc, deptDesc, compTypeMas.Plant, loc_desc 
                                    FROM [complaint].[dbo].[compTypeMas] 
                                    JOIN [complaint].[dbo].[emp_mas_sap] ON compTypeMas.Plant = emp_mas_sap.location
                                    where deptID='1235' and compTypeMas.Plant='NS04'"; 

                                $result2 = sqlsrv_query($conn, $datefind);

                                if ($result2) {
                                    while ($row1 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
                                        $compTypeId = $row1['compTypeId'];
                                        $plant = $row1['loc_desc'];
                                        $deptID = $row1['deptID'];
                                        $compTypeDesc = $row1['compTypeDesc'];
                                        

                                        $pendingQuery = "SELECT COUNT(compNo) AS Pending FROM complaintTable 
                                            WHERE STATUS != 'C' AND compTypeID = '$compTypeId' AND (compDate BETWEEN '$startDate' AND '$endDate') AND CompDeptID = '8000' AND plant='NS04'";

                                        $completedQuery = "SELECT COUNT(compNo) AS Completed FROM complaintTable 
                                            WHERE STATUS = 'C' AND compTypeID = '$compTypeId' AND (compDate BETWEEN '$startDate' AND '$endDate') AND CompDeptID = '8000' AND plant='NS04'";

                                        $pendingResult = sqlsrv_query($conn, $pendingQuery);
                                        $completedResult = sqlsrv_query($conn, $completedQuery);

                                        if ($pendingResult && $completedResult) {
                                            $pendingCount = sqlsrv_fetch_array($pendingResult)['Pending'];
                                            $completedCount = sqlsrv_fetch_array($completedResult)['Completed'];
                                            $total = $pendingCount + $completedCount;

                                            echo '<tr align="center">';
                                            //echo '<td>' . $compTypeId . '</td>';
                                            echo '<td>' . $row1['compTypeDesc'] . '</td>';
                                            echo '<td>' . $total . '</td>';
                                            echo '<td>' . $completedCount . '</td>';
                                            echo '<td>' . $pendingCount . '</td>';
                                            echo '</tr>';
                                        } else {
                                            echo "Error executing the queries: " . sqlsrv_errors();
                                        }
                                    }
                                }
                                ?>

                                <?php if ($noRecords) { ?>
                                    <tr>
                                        <td colspan="6">No records found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </center>

    <?php include 'footer.php';?>
</body>
</html>

<?php
sqlsrv_close($conn);
?>
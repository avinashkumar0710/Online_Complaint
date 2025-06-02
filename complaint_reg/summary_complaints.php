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
        overflow: auto;
    }
    </style>
</head>

<body>
    <center>
        <div class="container">
            <h5><b><u>Summary Complaints</u></b></h5>
            <form method="POST">
                <label><i><u>Please Select Date:</u></i></label>&nbsp;
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
                <input type="submit" value="Generate Report">
            </form>

            <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { 
            $startDate = $_POST["start_date"];
            $endDate = $_POST["end_date"];
            ?>

            <span style="text-decoration: underline;">Reports date</span>&nbsp;: &nbsp; <span
                style="font-style: italic; background-color: yellow;"><?php echo $startDate; ?></span> from <span
                style="font-style: italic; background-color: yellow;"><?php echo $endDate; ?></span>

            <?php
$noRecords = false;

if ($noRecords) { ?>

            <?php } else { ?><br><br>
            <div style="height: 650px; overflow: auto;">
                <table class="table table-striped" align="center" border="1.5" cellspacing="0">
                    <thead style="position: sticky; top: 0; background-color: #c3e6cb;">
                        <tr align="center">
                            <!-- <th style="padding: 8px;">Complaint ID</th> -->
                            <th style="padding: 8px;">Complaint Type</th>
                            <th style="padding: 8px;">Total No. of Complaints</th>
                            <th style="padding: 8px;">Attended</th>
                            <th style="padding: 8px;">Pending</th>
                            <th style="padding: 8px;">Plant</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
$datefind = "SELECT DISTINCT compTypeId, deptID, compTypeDesc, deptDesc, compTypeMas.Plant, loc_desc 
             FROM [complaint].[dbo].[compTypeMas] 
             JOIN [complaint].[dbo].[emp_mas_sap] ON compTypeMas.Plant = emp_mas_sap.location
             WHERE  compTypeMas.plant = '$plant'";

$result2 = sqlsrv_query($conn, $datefind);

if ($result2) {
    // Loop through the result set
    while ($row1 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
        $compTypeId = $row1['compTypeId'];
        $compTypeDesc = $row1['compTypeDesc'];
        $plant = $row1['loc_desc'];
        

        // Query to get pending and completed complaint counts
        $pendingQuery = "SELECT COUNT(compNo) AS Pending FROM complaintTable 
                         WHERE STATUS != 'C' AND compTypeID = '$compTypeId' AND (compDate BETWEEN '$startDate' AND '$endDate')";

        $completedQuery = "SELECT COUNT(compNo) AS Completed FROM complaintTable 
                           WHERE STATUS = 'C' AND compTypeID = '$compTypeId' AND (compDate BETWEEN '$startDate' AND '$endDate')";

        $pendingResult = sqlsrv_query($conn, $pendingQuery);
        $completedResult = sqlsrv_query($conn, $completedQuery);

        

        if ($pendingResult && $completedResult) {
            $pendingCount = sqlsrv_fetch_array($pendingResult)['Pending'];
            $completedCount = sqlsrv_fetch_array($completedResult)['Completed'];

            $total = $pendingCount + $completedCount;

            // Query to select relevant data from ComplaintTable
            $strSelComp = "SELECT COUNT(*) AS TotalCount FROM ComplaintTable CT 
                           WHERE CT.CompDate >= '$startDate' AND CT.CompDate <= '$endDate' 
                           AND CT.compTypeID = '$compTypeId'";

            $objRS = sqlsrv_query($conn, $strSelComp);

            if ($objRS === false) {
                // Handle query execution errors
                echo "Error executing the query: " . sqlsrv_errors();
            } else {
                if (sqlsrv_has_rows($objRS)) {
                    $noRecords = false; // At least one record found

                    echo '<tr align="center">';
                    echo '<td>' . $compTypeDesc . '</td>';
                    echo '<td>' . $total . '</td>';
                    echo '<td>' . $completedCount . '</td>';
                    echo '<td>' . $pendingCount . '</td>';
                    echo '<td>' . $row1['loc_desc'] . '</td>';
                    echo '</tr>';
                } else {
                    $noRecords = true;
                }
            }
        } else {
            // Handle query execution errors
            echo "Error executing the queries: " . sqlsrv_errors();
        }
    }
} else {
    // Handle query execution errors
    echo "Error executing the query: " . sqlsrv_errors();
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
</body>
</html>
<?php include 'footer.php';?>
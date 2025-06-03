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

    /* Modal Styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        top: 50%;
        left: 40%;
        width: 60%;
        height: 100%;
        background-color: #D0ECE7;
        border: 1px solid #ccc;
        box-shadow: -2px 2px 10px rgba(0, 0, 0, 0.2);
        padding: 20px;
        transform: translateY(-50%);
    }

    .modal-content {
        height: 100%;
        width: 100%;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 20px;
        position: relative;
        text-align: center;
        
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
    }

    .modal-close {
        
        border-radius: 0px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        background-color: #cf8e8e;
        border: none;

        position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 0px;
    
    color: white;
    
    font-size: 18px;
    text-align: center;
    }
    

    .modal-close img {
        width: 16px;
        height: 16px;
        vertical-align: middle;
    }
    </style>
</head>
<script>
function togglePlantRows(plant) {
    var rows = document.querySelectorAll('[data-plant="' + plant + '"]'); // Select rows by data attribute
    rows.forEach(row => {
        row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
    });
}
</script>
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
                     GROUP BY compTypeMas.Plant, compTypeId, deptID, compTypeDesc, deptDesc, loc_desc";
        $result2 = sqlsrv_query($conn, $datefind);

        if ($result2) {
            $plants = []; // Store unique plant names

            while ($row1 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
                $compTypeId = $row1['compTypeId'];
                $compTypeDesc = $row1['compTypeDesc'];
                $plant = $row1['loc_desc'];

                // Track unique plants
                if (!in_array($plant, $plants)) {
                    $plants[] = $plant;

                    // Create a clickable plant row
                    echo "<tr align='center' style='cursor: pointer; background-color: #90ee90;' onclick='togglePlantRows(\"$plant\")'>";
                    echo "<td colspan='5'><b>$plant</b> (Click to Expand/Collapse)</td>";
                    echo "</tr>";
                }

                // Fetch complaint counts
                $pendingQuery = "SELECT COUNT(compNo) AS Pending FROM complaintTable 
                                 WHERE STATUS != 'C' AND compTypeID = '$compTypeId' 
                                 AND (compDate BETWEEN '$startDate' AND '$endDate')";
                $completedQuery = "SELECT COUNT(compNo) AS Completed FROM complaintTable 
                                   WHERE STATUS = 'C' AND compTypeID = '$compTypeId' 
                                   AND (compDate BETWEEN '$startDate' AND '$endDate')";

                $pendingResult = sqlsrv_query($conn, $pendingQuery);
                $completedResult = sqlsrv_query($conn, $completedQuery);
                $pendingCount = $pendingResult ? sqlsrv_fetch_array($pendingResult)['Pending'] : 0;
                $completedCount = $completedResult ? sqlsrv_fetch_array($completedResult)['Completed'] : 0;
                $total = $pendingCount + $completedCount;

                echo "<tr align='center' data-plant='$plant' style='display: none;'>";
                
                echo "<td><a href='#' onclick='fetchComplaintData(\"$compTypeId\", \"$compTypeDesc\", \"$startDate\", \"$endDate\", \"$plant\")'>$compTypeDesc</a></td>";

                echo "<td>$total</td>";
                echo "<td>$completedCount</td>";
                echo "<td>$pendingCount</td>";
                echo "<td>$plant</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found.</td></tr>";
        }
        ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
    </center>


    <!-- Modal Structure -->
    <div id="complaintModal" class="modal">
    <div class="modal-content">
        
        <span class="close" onclick="closeModal()">&times;</span>
        <h4 style="text-align: center; font-weight: bold;">Complaint Type: <span id="complaintType"></span></h4>
        <h6 style="text-align: center; margin-top: -2px;">Start date: <u><span id="startDate" style="background-color:yellow;"></span></u> to End date: <u><span id="endDate" style="background-color:yellow;"></span></u></h6>

        <div id="modalBody"></div>
        <button class="modal-close" onclick="closeModal()">
            <img src="images/icons8-close.gif" alt="Close">&nbsp;&nbsp;CLOSE
        </button>
    </div>
</div>

<script>

function fetchComplaintData(compTypeId, compTypeDesc, startDate, endDate, plant) {
        document.getElementById("complaintType").innerText = compTypeDesc;
        document.getElementById("startDate").innerText = startDate;
        document.getElementById("endDate").innerText = endDate;

        fetch("overallcomplaintDetails.php?compTypeId=" + compTypeId + "&compTypeDesc=" + encodeURIComponent(compTypeDesc) + 
              "&startDate=" + startDate + "&endDate=" + endDate + "&plant=" + encodeURIComponent(plant))
            .then(response => response.text())
            .then(data => {
                document.getElementById("modalBody").innerHTML = data;
                document.getElementById("complaintModal").style.display = "block";
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    function closeModal() {
        document.getElementById("complaintModal").style.display = "none";
    }
</script>
</body>
</html>
<?php
if (isset($_GET['compTypeId'], $_GET['compTypeDesc'], $_GET['startDate'], $_GET['endDate'])) {
    $compTypeId = $_GET['compTypeId'];
    $compTypeDesc = $_GET['compTypeDesc'];
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    // Database connection
    $serverName = "192.168.100.240";
    $connectionOptions = array(
        "Database" => "complaint",
        "UID" => "sa",
        "PWD" => "Intranet@123"
    );
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if (!$conn) {
        die("Connection failed: " . print_r(sqlsrv_errors(), true));
    }

    // Fetch complaints based on received values
    $query = "SELECT CompNo, CompDate, CompOriginDeptID, CompDeptID, compTypeID, Description, 
                     Location, ContactNo, CompUserID, Status, TimeStamp, Time, Plant 
              FROM [Complaint].[dbo].[ComplaintTable] 
              WHERE compTypeID = ? AND status IN ('R','N') AND (compDate BETWEEN '$startDate' AND '$endDate')";

    $params = array($compTypeId);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $currentDate = new DateTime(); // Get the current date
$noRecords = true; // Assume no records initially

    echo "<div style='height: 700px; overflow: auto;'>";
    echo "<table class='table table-striped' align='center' border='1.5' cellspacing='0'>";
    echo "<thead style='position: sticky; top: 0; background-color: #c3e6cb;'>";
    echo "<tr align='center'>
            <th>Complaint Date</th>
            
            <th>Department</th>
            <th>Description</th>
           
            <th>Status</th>
            <th>Plant</th>
            <th>Days Pending</th> <!-- New Column -->
          </tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $noRecords = false; // Found records, so change flag


        // Calculate Days Pending
    $compDate = $row['CompDate'];
    $daysPending = $currentDate->diff($compDate)->days;

        // Map Status values
        $statusText = ($row['Status'] == 'N') 
    ? "<span style='color: #FF0000;'>(New)</span>" 
    : (($row['Status'] == 'R') 
        ? "<span style='color: #FF9900;'>(Remarked)</span>" 
        : $row['Status']);

    
        // Map Plant codes to location names
        $plantMap = [
            'NS04' => 'Bhilai',
            'NS03' => 'Rourkela',
            'NS02' => 'Durgapur',
            'NS01' => 'Corporate Center'
        ];
        $plantName = isset($plantMap[$row['Plant']]) ? $plantMap[$row['Plant']] : $row['Plant'];
    
        // Fetch Department Name
        $deptID = $row['CompDeptID'];
        $deptQuery = "SELECT deptDesc FROM [Complaint].[dbo].[compTypeMas] WHERE deptID = ?";
        $deptStmt = sqlsrv_query($conn, $deptQuery, [$deptID]);
        $deptDesc = ($deptStmt && $deptRow = sqlsrv_fetch_array($deptStmt, SQLSRV_FETCH_ASSOC)) ? $deptRow['deptDesc'] : $deptID;
    
        echo "<tr align='center'>
                <td>" . $row['CompDate']->format('Y-m-d') . "</td>
               
                <td>" . $deptDesc . "</td>  <!-- Fetching department name -->
                <td>" . $row['Description'] . "</td>
               
                <td>" . $statusText . "</td>  <!-- Mapping N/R to meaningful labels -->
                <td>" . $plantName . "</td>  <!-- Mapping plant codes -->
                <td><button style='background: linear-gradient(to right, #e33b3b, #e33b3b); 
                color: white; 
                border: none; 
                border-radius: 10px; 
                padding: 5px 12px; 
                font-size: 12px; 
                font-weight: bold; 
                cursor: default;'>" . $daysPending . " days </button></td> <!-- New column -->
              </tr>";
    }
    

echo "</tbody>";
echo "</table>";

if ($noRecords) {
    echo "<p style='text-align: center; font-weight: bold; color: red;'>No pending complaints in selected dates.</p>";
}


echo "</div>";
echo "<p style='text-align: center; margin-top: 20px; font-weight: bold; padding: 10px; background-color: #D0ECE7; 
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-radius: 5px; font-size:15px;'>
        Complaint Status Legend: 
        <span style='color: #FF0000;'>N</span> - Newly Registered Complaint, 
        <span style='color: #FF9900;'>R</span> - Maintenance department Seen and Remarked the complaint, 
       
      </p>";

    sqlsrv_close($conn);
}
?>

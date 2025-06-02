<?php
// Establish a database connection (replace with your own database connection code)
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

// Check if the 'plant' parameter is set in the AJAX request
if (isset($_GET['plant'])) {
    $selectedPlant = $_GET['plant'];

    // Query the database to fetch department options based on the selected plant
    $query = "SELECT DISTINCT [Dept_id], [DeptName] FROM [Complaint].[dbo].[EA_DeptCode_Mas] WHERE plant = ?";
    $params = array($selectedPlant);

    $result = sqlsrv_query($conn, $query, $params);

    if ($result) {
        $departments = array();

        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $departments[] = $row;
        }

        // Return the department data as JSON
        header('Content-Type: application/json');
        echo json_encode($departments);
    } else {
        echo "Error executing the query: " . sqlsrv_errors();
    }
}

// Close the database connection
sqlsrv_close($conn);
?>

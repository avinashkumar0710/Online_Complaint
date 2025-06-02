<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["term"])) {
        $searchInput = $_POST["term"];

        // Connect to the database
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

        // Build the query to fetch the matching employee names
        $query = "SELECT emp_name FROM EA_webuser_tstpp WHERE emp_name LIKE '%$searchInput%'";
        $result = sqlsrv_query($conn, $query);

        // Check if the query was successful
        if ($result) {
            $employeeNames = array();
            // Fetch all matching rows
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $employeeNames[] = $row['emp_name'];
            }
            // Return the matching employee names as a JSON response
            echo json_encode($employeeNames);
        } else {
            // Handle the case when the query fails
            echo json_encode(array());
        }

        // Close the database connection
        sqlsrv_close($conn);
    }
}
?>

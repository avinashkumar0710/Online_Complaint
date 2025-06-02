<?php
try {
    // Database connection settings
    $serverName = "192.168.100.240";
    $connectionOptions = array(
        "Database" => "complaint",
        "UID" => "sa",
        "PWD" => "Intranet@123"
    );

    // Establish the connection
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . print_r(sqlsrv_errors(), true)); // Corrected error handling
    }

    // SQL query to fetch all columns
    $selectSql = "SELECT * FROM [Complaint].[dbo].[EA_webuser_tstpp]
    WHERE RIGHT(emp_num, 6) NOT IN (
        SELECT RIGHT(empno, 6)
        FROM [Complaint].[dbo].[emp_mas_sap]
        WHERE status = 'A'
    )
    AND status NOT IN (' ', 'O', 'S')";

$selectQuery = sqlsrv_query($conn, $selectSql);

if (!$selectQuery) {
die("SELECT query execution failed: " . print_r(sqlsrv_errors(), true));
}

// Check if data exists
if (sqlsrv_has_rows($selectQuery)) {
// Data exists, so perform the UPDATE
$updateSql = "UPDATE [Complaint].[dbo].[EA_webuser_tstpp]  SET status = ' '
        WHERE RIGHT(emp_num, 6) NOT IN (
            SELECT RIGHT(empno, 6)
            FROM [Complaint].[dbo].[emp_mas_sap]
            WHERE status = 'A'
        )
        AND status NOT IN (' ', 'O', 'S')";

$updateQuery = sqlsrv_query($conn, $updateSql);

if (!$updateQuery) {
die("UPDATE query execution failed: " . print_r(sqlsrv_errors(), true));
}

echo "Data updated successfully!";
} else {
echo "No data to update.";
}

// Close the database connection
sqlsrv_close($conn);
} catch (Exception $e) {
echo "Error: " . $e->getMessage();
}
?>

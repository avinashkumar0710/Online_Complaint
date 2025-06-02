<?php
try {
    // ... (your database connection code here) ...
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
// $selectSql = "
// select EA_webuser_tstpp.emp_num, emp_name,dept , EA_webuser_tstpp.Plant ,dept_code ,[emp_mas_sap].location,[emp_mas_sap].dept  ,EA_DeptCode_Mas.Dept_id
// FROM [Complaint].[dbo].[emp_mas_sap]
// join EA_webuser_tstpp on right(emp_mas_sap.empno,6) = EA_webuser_tstpp.emp_num
// join EA_DeptCode_Mas on [EA_DeptCode_Mas].DeptName = [emp_mas_sap].dept and [EA_DeptCode_Mas].Plant = [emp_mas_sap].location
// where  EA_webuser_tstpp.status not in (' ' ,'O' ,'S') 
//  and emp_mas_sap.status = 'A' 
//  and ( EA_DeptCode_Mas.dept_id <> EA_webuser_tstpp.dept_code or EA_webuser_tstpp.Plant <> [emp_mas_sap].location )
//  order by name asc";

$selectSql="select EA_webuser_tstpp.emp_num, emp_name,dept , EA_webuser_tstpp.Plant ,dept_code ,[emp_mas_sap].location,[emp_mas_sap].dept  ,EA_DeptCode_Mas.Dept_id
FROM [Complaint].[dbo].[emp_mas_sap]
join EA_webuser_tstpp on emp_mas_sap.empno = ( case when EA_webuser_tstpp.emp_num > 999999 then EA_webuser_tstpp.emp_num
else
CONCAT('00',EA_webuser_tstpp.emp_num) 
end )
join EA_DeptCode_Mas on [EA_DeptCode_Mas].DeptName = [emp_mas_sap].dept and [EA_DeptCode_Mas].Plant = [emp_mas_sap].location
where  EA_webuser_tstpp.status not in (' ' ,'O','S' ) 
 and emp_mas_sap.status = 'A' 
 and ( EA_DeptCode_Mas.dept_id <> EA_webuser_tstpp.dept_code or EA_webuser_tstpp.Plant <> [emp_mas_sap].location )
 order by name asc";

    $selectQuery = sqlsrv_query($conn, $selectSql);

    if (!$selectQuery) {
        die("SELECT query execution failed: " . print_r(sqlsrv_errors(), true));
    }

    // Check if data exists
    if (sqlsrv_has_rows($selectQuery)) {
        // Data exists, so perform the UPDATE
        while ($row = sqlsrv_fetch_array($selectQuery, SQLSRV_FETCH_ASSOC)) {
            $deptId = $row['Dept_id'];
            $empNum = $row['emp_num'];
            $location = $row['location'];

            //print 'dept<br>' .$deptId;
            //print  'empnum<br>' .$empNum;
            //print 'loc<br>' .$location;

            // Update query with placeholders
             $updateSql = "UPDATE [Complaint].[dbo].[EA_webuser_tstpp] SET dept_code = ? , Plant= '$location' WHERE EA_webuser_tstpp.emp_num = ?";

             $updateParams = array($deptId, $empNum);

             $updateQuery = sqlsrv_query($conn, $updateSql, $updateParams);

             if (!$updateQuery) {
                 die("UPDATE query execution failed: " . print_r(sqlsrv_errors(), true));
             }
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

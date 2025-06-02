<?php
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
if (!$conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

// SQL query to fetch all columns
// $sql = "SELECT distinct empno, name, location, grade, design, orgeh, dept, dob, email, mob1, mob2, ret_date, loc_desc, img_ext, rank, status, Dept_id  from [Complaint].[dbo].[emp_mas_sap] join [Complaint].[dbo].[EA_DeptCode_Mas]  on 
// [emp_mas_sap].dept=[EA_DeptCode_Mas].DeptName and [emp_mas_sap].location=[EA_DeptCode_Mas].plant
//       WHERE RIGHT(empno, 6) NOT IN (
//              SELECT RIGHT(emp_num, 6)
//              FROM [Complaint].[dbo].[EA_webuser_tstpp]
//              WHERE status NOT IN (' ', 'O', 'S')) AND status = 'A'";

$sql = "SELECT  emp_mas_sap.empno, name, location, grade, design, orgeh, dept, dob, email, mob1, mob2, ret_date, loc_desc, img_ext, rank, status, Dept_id ,EA_DeptCode_Mas.DeptName
FROM [Complaint].[dbo].[emp_mas_sap]
join EA_DeptCode_Mas on [EA_DeptCode_Mas].DeptName = [emp_mas_sap].dept and [EA_DeptCode_Mas].Plant = [emp_mas_sap].location
where emp_mas_sap.status = 'A'  and  emp_mas_sap.empno not in ( select 
 ( case when EA_webuser_tstpp.emp_num > 999999 then EA_webuser_tstpp.emp_num
else
CONCAT('00',EA_webuser_tstpp.emp_num) 
end )
from EA_webuser_tstpp  
where EA_webuser_tstpp.status not in (' ' ,'O','S' ))";

// Execute the query
$query = sqlsrv_query($conn, $sql);

if (!$query) {
    die("Query execution failed: " . print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
    // Bind the parameters
    $empNum = ltrim($row['empno'], '0');
    $passwd = ltrim($row['empno'], '0');
    $empName = $row['name'];
    $designationCode = $row['design'];
    $deptCode = $row['Dept_id'];
    $status = $row['status'];
    $plant = $row['location'];
    //echo '$empNum' .$empNum, '$passwd' .$passwd ,' $empName' . $empName, ' $designationCode' . $designationCode, '$deptCode' .$deptCode, '$status' .$status, '$plant' .$plant;

    //echo "EmpNo: " . $empNum . "<br>";
    //echo "Passwd: " . $passwd . "<br>";
    //echo "EmpName: " . $empName . "<br>";
    //echo "designationCode: " . $designationCode . "<br>";
    //echo "deptCode: " . $deptCode . "<br>";
    //echo "status: " . $status . "<br>";
    //echo "plant: " . $plant . "<br>";

    // Prepare an INSERT statement for EA_webuser_tstpp
    $insertSql = "INSERT INTO [Complaint].[dbo].[EA_webuser_tstpp]
                  ([emp_num], [passwd], [emp_name], [designation_code], [dept_code], [access], [status], [Plant])
                  VALUES (?, ?, ?, ?, ?, '0', ?, ?)";

    // Prepare the statement
    $insertStmt = sqlsrv_prepare($conn, $insertSql, array(&$empNum, &$passwd, &$empName, &$designationCode, &$deptCode, &$status, &$plant));

    if (!$insertStmt) {
        die("Statement preparation failed: " . print_r(sqlsrv_errors(), true));
    }

    // Execute the statement
    if (!sqlsrv_execute($insertStmt)) {
        $errors = sqlsrv_errors();
        if ($errors) {
            foreach ($errors as $error) {
                echo "Insertion failed: SQLSTATE: " . $error['SQLSTATE'] . " Error Code: " . $error['code'] . " Message: " . $error['message'];
            }
        } else {
            echo "Insertion failed for an unknown reason.";
        }
        // You can choose to exit here or handle the failure in some other way.
    } else {
        echo "Data inserted successfully!";
    }

    // Close the statement
    sqlsrv_free_stmt($insertStmt);
}

// Close the database connection
sqlsrv_close($conn);
?>

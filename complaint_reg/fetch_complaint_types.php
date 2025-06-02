<?php
$comdept = !empty($_POST['comdept']) ? $_POST['comdept'] : '';
//echo "<script>alert('comdept: $comdept');</script>";

if (!empty($comdept)) {
    // $comdept is not empty, proceed with database connection and query
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

    echo "<script>alert('Location: $comdept');</script>";
    print $comdept;

    $plantQuery = "SELECT Plant, emp_num, dept_code FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$_SESSION[emp_num]'";
$plantResult = sqlsrv_query($conn, $plantQuery);
$userId = $_SESSION['emp_num'];
if ($plantResult) {
    $row = sqlsrv_fetch_array($plantResult, SQLSRV_FETCH_ASSOC);
    $plant = $row['Plant'];}

    $query = "SELECT DISTINCT compTypeDesc, compTypeId FROM compTypeMas WHERE deptDesc = ? and plant='$plant'";
    $params = array($comdept);
    $comTypeData = sqlsrv_query($conn, $query, $params);

    if ($comTypeData !== false) {
        $options = "<option value=''>Select Complaint Type</option>";

        while ($row = sqlsrv_fetch_array($comTypeData, SQLSRV_FETCH_ASSOC)) {
            $compTypeDesc = $row['compTypeDesc'];
            $compTypeId = $row['compTypeId'];
            $options .= "<option value='$compTypeId'>$compTypeDesc</option>";
            //echo "<script>alert('comdept: $compTypeDesc');</script>";
        }

        echo $options;
    } else {
        echo "<option value=''>No Complaint Types found</option>";
    }

    sqlsrv_free_stmt($comTypeData);
    sqlsrv_close($conn);
} else {
    // $comdept is empty, handle the case where it's not passed correctly
    echo "<option value=''>Invalid or missing 'comdept' parameter</option>";
}
?>


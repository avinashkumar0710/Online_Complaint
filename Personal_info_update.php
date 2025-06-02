<?php
session_start();
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the updated field values
    $empno = $_POST['empno'] ?? '';
    $empname = $_POST['emp_name'] ?? '';
    $userDesig = $_POST['design'] ?? '';
    $userDeptDesc = $_POST['dept'] ?? '';
    $usermob1 = $_POST['mobile1'] ?? '';
    $usermob2 = $_POST['mobile2'] ?? '';
    $userdob = $_POST['dob'] ?? '';
    $userintoff = $_POST['inter_o'] ?? '';
    $userintres = $_POST['inter_r'] ?? '';
    $usermail = $_POST['email_id'] ?? '';
    $useroffice = $_POST['office_no'] ?? '';
    $userstdcode = $_POST['std_code'] ?? '';
    $useraddr = $_POST['address'] ?? '';
    $userstat = $_POST['status'] ?? '';
    $userresno = $_POST['res_no'] ?? '';
    $userstdres = $_POST['std_res'] ?? '';
    $userplant = $_POST['plant'] ?? '';
    $userorg = $_POST['org'] ?? '';
    $userqtr_type = $_POST['qtr_type'] ?? '';
    //$userblock_no = $_POST['block_no'];
    //$userqtr_no = $_POST['qtr_no'];
    $usergrade = $_POST['grade']?? '';
    
    
    // Set up connection parameters
    $serverName = "NSPCL-AD\SQLEXPRESS";
    $connectionOptions = array(
        "Database" => "Complaint",
        "UID" => "",
        "PWD" => ""
    );           
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    // Prepare the update statement
    $sql = "UPDATE emp_mas SET emp_name=?, design=?, dept=?, mobile1=?, mobile2=?,  dob=?, inter_o=?, inter_r=?, email_id=?, office_no=?, std_code=?, address=?,
     status=?, res_no=?, std_res=?, plant=?, org=?, qtr_type=?, grade=? WHERE empno=?";
    
    $params = array(
        $empname, $userDesig, $userDeptDesc, $usermob1, $usermob2, $userdob, $userintoff, $userintres, $usermail, $useroffice, $userstdcode, $useraddr, $userstat, 
        $userresno, $userstdres, $userplant, $userorg, $userqtr_type, $usergrade, $empno
    );
    
 
    // Execute the update statement
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Fields updated successfully.";
    }
    
    // Clean up resources
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    
    // Redirect back to the form or any other desired page after the update
    header('location:View_Personal_Info.php');
    exit();
}
?>

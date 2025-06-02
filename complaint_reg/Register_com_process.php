<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form field values
    $CompDate = $_POST['CompDate'];
    $CompUserID = $_POST['CompUserID'];
    $userdept = $_POST['userdept'];
    $comdept = $_POST['comdept'];
    $comTypeDesc = $_POST['comTypeDesc'];
    $Description = $_POST['Description'];
    $contactNo = $_POST['contactNo'];
    $Location = $_POST['Location'];
    //$selectedDepartment = $_POST['selecteditem'];
    // Set up connection parameters
    $serverName = "192.168.100.240";
    $connectionOptions = array(
        "Database" => "complaint",
        "UID" => "sa",
        "PWD" => "Intranet@123"
    );

    // Establish a connection
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    //maximum add to complaint table
    $CompNo = "SELECT max(CompNo) as maxCompNo FROM complaintTable";
    $CompNo1 = sqlsrv_query($conn, $CompNo);

    if ($CompNo1 === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $newComplaintNo = 1;
    if (sqlsrv_fetch($CompNo1) === true) {
        $maxCompNo = sqlsrv_get_field($CompNo1, 0);
        if ($maxCompNo !== null) {
            $newComplaintNo = intval(trim($maxCompNo)) + 1;
        }
    }
    

    //maximum add number to complaint attend table
    $Compatt = "SELECT max(CompNo) as maxCompNo FROM ComplaintAttendDet";
    $Compatt1 = sqlsrv_query($conn, $CompNo);

    if ($Compatt1 === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $newCompatt1 = 1;
    if (sqlsrv_fetch($Compatt1) === true) {
        $maxCompNo1 = sqlsrv_get_field($Compatt1, 0);
        if ($maxCompNo1 !== null) {
            $newCompatt1 = intval(trim($maxCompNo1)) + 1;
        }
    }
    date_default_timezone_set('Asia/Kolkata');
    function getCurrentTimestamp()
    {
        return date('Y-m-d H:i:s');
    }
    $timestamp = getCurrentTimestamp();

    function getCurrentTime()
    {
        return date('H:i:s');
    }
    $time = getCurrentTime();

    $plantQuery = "SELECT Plant, emp_num,dept_code FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$_SESSION[emp_num]'";
    $plantResult = sqlsrv_query($conn, $plantQuery);
    $userId = $_SESSION['emp_num'];

    if ($plantResult) {
        $row = sqlsrv_fetch_array($plantResult, SQLSRV_FETCH_ASSOC);
        $plant = $row['Plant'];
        $dept_code = $row['dept_code'];
    }

    $deptQuery = "SELECT DISTINCT deptID, deptDesc,compTypeDesc,compTypeId FROM compTypeMas WHERE plant = '$plant' AND compTypeId ='$comTypeDesc'";
    $deptResult = sqlsrv_query($conn, $deptQuery);

    if ($deptResult) {
        $row = sqlsrv_fetch_array($deptResult, SQLSRV_FETCH_ASSOC);
        $deptID = $row['deptID'];
        $deptDesc = $row['deptDesc'];
        $compTypeDesc = $row['compTypeDesc'];
        $compTypeId = $row['compTypeId'];
    }

    

    $status = 'N';

    $sql = "INSERT INTO ComplaintTable (CompNo, CompDate, CompOriginDeptID, CompDeptID, compTypeID, Description, Location,ContactNo,CompUserID, Status, TimeStamp, Time, Plant)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = array(
        $newComplaintNo, // maxCompNo
        $CompDate,
        $dept_code,
        $deptID,
        $compTypeId,
        $Description,
        $Location,
        $contactNo,
        $CompUserID,
        $status,
        $timestamp,
        $time,
        $plant
    );
    // echo "<script>alert('complaint no:$newComplaintNo');</script>";
    // echo "<script>alert('CompDate:$CompDate');</script>"; 
    // echo "<script>alert('CompOriginDeptID:$dept_code');</script>";
    // echo "<script>alert('CompDeptID:$deptID');</script>";
    // echo "<script>alert('compTypeId:$compTypeId');</script>";
    // echo "<script>alert('Description:$Description');</script>";
    // echo "<script>alert('Location:$Location');</script>";
    // echo "<script>alert('contactNo:$contactNo');</script>";
    // echo "<script>alert('CompUserID:$CompUserID');</script>";
    // echo "<script>alert('status:$status');</script>";
    // echo "<script>alert('timestamp:$timestamp');</script>";
    // echo "<script>alert('time:$time');</script>";
    // echo "<script>alert('Plant:$plant');</script>";

    $stmt = sqlsrv_query($conn, $sql, $params);

   if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Insert into ComplaintAttendDet
    //$attendedByUserID = ''; // Set the appropriate value for attendedByUserID
    //$attendedDate = date('Y-m-d'); // Set the attendedDate to the current date
    $remarks = 'NEWLY REGISTERED'; // Set the appropriate value for remarks

    $attendSql = "INSERT INTO ComplaintAttendDet (CompNo, AttendedByUserID, AttendedDate, Remarks, TimeStamp, Time)
                VALUES (?, ?, ?, ?, ?, ?)";
    $attendParams = array(
        $newCompatt1,
        $userId,
        $CompDate,
        $remarks,
        $timestamp,
        $time
    );
    $attendStmt = sqlsrv_query($conn, $attendSql, $attendParams);

    if ($attendStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_free_stmt($attendStmt);
    sqlsrv_close($conn);
    
    // Handle image upload
    if (isset($_FILES['complaintImage']) && $_FILES['complaintImage']['error'] === 0) {
        $imageFile = $_FILES['complaintImage'];
        
        // Check if the uploaded file is an image
        $imageFileType = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($imageFileType, $allowedExtensions)) {
            $newImageName = $newComplaintNo . '.' . $imageFileType;
            $imagePath = '../images/' . $newImageName;
    
            // Move the uploaded image to the images folder
            if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
                echo "Image uploaded successfully";
            } else {
                echo "Error uploading image.";
            }
        } else {
            echo "Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF.";
        }
    }

    // Show success message and reset the form
    echo "<script>
    alert('Complaint registered successfully.');
    window.location.href = 'Register_comp.php';
</script>";
exit;
} 

?>
<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
$empno = $_POST['usrno'] ;
  

 $uuid_get   = $_POST['uuid'] ; 
// Print or log the values for debugging
// echo "usrno: " . $empno . "<br>";
// echo "uuid: " . $uuid_get . "<br>";

 $checkauth = checkauth(); 

 if ( $checkauth == "F"){
 
   return ;
 }
 function checkauth() {
  $serverName = "192.168.100.240"; //serverName\instanceName
  $username = "complaint";
  $password = "firerose";
  $dbname = "Complaint";//your database name
  $mssqldriver = '{SQL Server}'; 
  $mssqldriver = '{SQL Server Native Client 11.0}';
  $mssqldriver = '{ODBC Driver 11 for SQL Server}';
  $conn=new PDO("odbc:Driver=$mssqldriver;server=$serverName;database=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $stmt = $conn->prepare("Select  top 1 *  from mobapp_user where uuid ='".$GLOBALS['uuid_get']."' and safety = 'X'");
  // $stmt = $conn->prepare("Select  top 1 *  from mobapp_user as a join mob_usr_admin as b on a.userno = b.empno  where a.uuid ='".$GLOBALS['uuid_get']."' and a.userno ='".$GLOBALS['empno']. "' and a.safety = 'X' and app = 'PEPTALK' ");
  $stmt->execute();
if ($data = $stmt->fetch()) {
return "T";
}
else{
  return "F";
}
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the POST request
    $deptId = $_POST['deptId'];
    $compTypeId = $_POST['compTypeId'];
    $location = $_POST['location'];
    $contactNo = $_POST['contactNo'];
    $description = $_POST['description'];
    $plant = $_POST['plant'];
    $deptCode = $_POST['deptCode'];
    $maxCompNoComplaintTable = $_POST['maxCompNoComplaintTable'];
    $maxCompNoComplaintAttendDet = $_POST['maxCompNoComplaintAttendDet'];
    $uuid = $_POST['uuid'];
    $empno = $_POST['usrno'];

    

    // Your database connection parameters
    $serverName = "192.168.100.240";
$username = "complaint";
$password = "firerose";
$dbname = "Complaint";
$mssqldriver = '{SQL Server}'; 
$mssqldriver = '{SQL Server Native Client 11.0}';
$mssqldriver = '{ODBC Driver 11 for SQL Server}';

// Allow requests from any origin
header('Access-Control-Allow-Origin: *');

// Allow the following HTTP methods
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Allow the following headers
header('Access-Control-Allow-Headers: Content-Type');

    try {
        // Create a PDO connection
        $conn = new PDO("odbc:Driver=$mssqldriver;server=$serverName;database=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $status = 'N';
        $remarks = 'NEWLY REGISTERED';

        date_default_timezone_set('Asia/Kolkata');
        function getCurrenttimeStamp()
        {
            return date('Y-m-d H:i:s');
        }
        $timestamp = getCurrenttimeStamp();

        function currenttime()
        {
            return date('H:i:s');
        }
        $currentTime = currenttime();
        // Prepare and execute the SQL query to insert data into the 'complainttable' table
        $query = "INSERT INTO ComplaintTable (CompNo, CompDate, CompOriginDeptID, CompDeptID, compTypeID, Location, ContactNo, Description, CompUserID, Status, Timestamp, Time, Plant)
                  VALUES ('$maxCompNoComplaintTable', '$timestamp', '$deptCode', '$deptId', '$compTypeId', '$location','$contactNo' , '$description', '$empno','$status', '$timestamp',' $currentTime','$plant')";
        $stmt = $conn->prepare($query);
        $stmt->execute([$maxCompNoComplaintTable, $timestamp, $deptCode, $deptId, $compTypeId, $location, $contactNo, $description, $empno, $status,$timestamp,$currentTime,$plant]);


        $attendSql = "INSERT INTO ComplaintAttendDet (CompNo, AttendedByUserID, AttendedDate, Remarks, TimeStamp, Time)
                VALUES ('$maxCompNoComplaintAttendDet', '$empno', '$timestamp', '$remarks', '$timestamp', '$currentTime')";
                $attendParams= $conn->prepare($attendSql);
                $attendParams->execute([$maxCompNoComplaintAttendDet, $empno, $timestamp, $remarks, $timestamp, $currentTime]);

        // Send a success response to the client
        echo json_encode(['success' => true]);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageFile = $_FILES['image'];

            // Check if the uploaded file is an image
        $imageFileType = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

            // Specify the destination directory on your server (HTTP path)
            if (in_array($imageFileType, $allowedExtensions)) {
                $newImageName = $maxCompNoComplaintTable . '.' . $imageFileType;
            $uploadDir = 'images/' . $newImageName;
        
            // Generate a unique filename to avoid overwriting existing files
            //$uniqueFilename = uniqid('image_') . '_' . basename($_FILES['image']['name']);
        
            // Specify the full HTTP path for the uploaded file
            $uploadPath = $uploadDir;
        
            // Move the uploaded file to the destination directory
            if (move_uploaded_file($imageFile['tmp_name'], $uploadDir)) {
                // File upload successful
                echo 'File uploaded successfully.';
            } else {
                // Error handling for file upload failure
                echo 'Error uploading file.';
            }
        } else {
            // Handle cases where no file is uploaded or an error occurs
            echo 'No file uploaded or an error occurred.';
        }
        }
       
        
    
        // ... (continue with your existing code)
    
    } catch (PDOException $e) {
        // Handle any database connection or query errors
        echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
    }}
?>
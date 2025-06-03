<?php 
// start a new session
session_start();
if (!isset($_SESSION["emp_num"])) {   
        header("location:../login.php");
    }

    // Database Connection
    $serverName = "192.168.100.240";
    $connectionInfo = array(
        "Database" => "complaint",
        "UID" => "sa",
        "PWD" => "Intranet@123"
    );            
    $conn = sqlsrv_connect($serverName, $connectionInfo);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Set the "Plant" cookie
$plant = "select Plant from [Complaint].[dbo].[EA_webuser_tstpp] where emp_num='$_SESSION[emp_num]'"; // Replace with the actual plant value


?>
<!---------------------------------Start Header Area------------------------------------>
<html>

<head>
  
    <link rel="icon" href="images/feedback.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Cantarell&display=swap');

    .my-custom-scrollbar {
        position: relative;
        height: 400px;
        overflow: auto;
        width: 650px;
        border-radius: 10px;
        border: 1px solid black;
        box-shadow: 5px 5px 5px #888888;
    }

    #dtBasicExample {
        border-radius: 25px;
        border: 2px solid yellowgreen;
    }

    .nav-link {
    color: #F8F9F9;
    transition: color 0.3s ease;
}

.nav-link:hover {
    color: #BA32C1;
}
    </style>
</head>


    <?php           
            // Check if the user is authenticated
            if (!isset($_SESSION["emp_num"])) {
                header("location: ../login.php");
                exit;
            }

            $name = "SELECT emp_name, access FROM EA_webuser_tstpp WHERE emp_num = ?";
            $params = array($_SESSION['emp_num']);
            $stmt = sqlsrv_query($conn, $name, $params);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_has_rows($stmt)) {
                // Get the user name from the result set
                 $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                $username = $row['emp_name'];
                $access = $row['access'];
            } 
    ?>

<body style="font-family: 'Cantarell', sans-serif;">
    <div class='card text-center'>
        <div class='card-header'>
            WELCOME , <?php echo $username; ?> to <i><SPAN style='background-color:yellow'> <u>O</u>NLINE
                    <u>C</u>OMPLAINT <u>M</u>ANAGEMENT <u>S</u>YSTEM</SPAN></i>&nbsp;&nbsp;
            <a href='../logout.php'><input type='submit' class='btn btn-success btn-sm' value='LOGOUT'></a>&nbsp;
        </div>
    </div>

    <ul class='nav justify-content-center' style='background-color: #34495E;'>

    <?php if ($access == '17' || $access == '4' || $access == '7' || $access == '0'): ?>
    <li class='nav-item'>
        <a class='nav-link' href='../index.php'>OCMS Home</a>
    </li>
    <?php endif; ?>  

  <?php if ($access == '17' || $access == '4' || $access == '7' || $access == '0' || $access == '25'): ?>
    <li class='nav-item'>
        <a type='button' class='nav-link dropdown-toggle' data-bs-toggle='dropdown'>Complaints Register</a>
        <ul class='dropdown-menu' style='background-color: #8be385;'>
            <li><a class='dropdown-item' href='Register_comp.php'>Register New Complaint</a></li>
            <li><a class='dropdown-item' href='Check_status.php'>Check Status Of Your Complaint</a></li>
        </ul>
    </li>
    <?php endif; ?>  
    <!-- <li class='nav-item'>
        <a class='nav-link' href='../View_Personal_Info.php'>View & Modify Personal Info</a>
    </li> -->

    <?php if ($access == '17' || $access == '4' || $access == '7'): ?>
        <li class='nav-item'>
            <a type='button' class='nav-link dropdown-toggle' data-bs-toggle='dropdown'>Department's Complaints</a>
            <ul class='dropdown-menu' style='background-color: #8be385;'>
                <li><a class='dropdown-item' href='pending_depart.php'>Check Pending Complaints for Your Department</a></li>
                <li><a class='dropdown-item' href='complaint_Dept.php'>Check Complaints Attended By Your Department</a></li>
            </ul>
        </li>
    <?php endif; ?>

    <?php if ($access == '17' || $access == '7'): ?>
        <li class='nav-item'>
            <a class='nav-link' href='others_depart_complain.php'>Complaints of Other Deptt</a>
        </li>

        <li class='nav-item'>
            <a type='button' class='nav-link dropdown-toggle' data-bs-toggle='dropdown'>Reports</a>
            <ul class='dropdown-menu' style='background-color: #8be385;'>
                <li><a class='dropdown-item' href='employee_wise_report.php'>Employee Wise Reports</a></li>
                <li><a class='dropdown-item' href='pending_complaints_details.php'>List of Pending Complaints with DESCRIPTION</a></li>
                <li><a class='dropdown-item' href='pending_complaints_department.php'>Pending Complaints For Your Department</a></li>
                <li><a class='dropdown-item' href='summary_complaints.php'>Summary of Complaints</a></li>
                <li><a class='dropdown-item' href='department_wise_summary.php'>Department Wise Summary of Complaints</a></li>
                <?php
        // Conditionally display the IT Summary link
        if (isset($_SESSION["emp_num"]) && $_SESSION["emp_num"] == '99999999') {
        ?>
            <li><a class='dropdown-item' href='department_wise_ITsummary.php'>Department Wise Summary of Complaints (IT)</a></li>
        <?php
        }
        ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if ($access == '17'):?>

        <li class='nav-item'>
            <a type='button' class='nav-link dropdown-toggle' data-bs-toggle='dropdown'>Administration Menu</a>
            <ul class='dropdown-menu' style='background-color: #8be385;'>
                <li><a class='dropdown-item' href='manage_compt.php'>Manage Complaint Type Master</a></li>
                <li><a class='dropdown-item' href='manage_user_master.php'>Manage User Master</a></li>
                <li><a class='dropdown-item' href='user_request.php'>New user Requests</a></li>
            </ul>
        </li>
    <?php endif; ?>

    <?php if ($access == '25'):?>
        <li class='nav-item'>
            <a class='nav-link' href='overall_summary_complaints.php'>Summary of Complaints</a>
        </li>
        <?php endif; ?>

    <li class='nav-item'>
        <a class='nav-link' href='Change_My_Password.php'>Change my Password</a>
    </li>

</ul>       
</body>
</html>
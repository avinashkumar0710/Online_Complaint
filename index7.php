<?php 
//ini_set('session.gc_maxlifetime', 1800);
// start a new session
session_start();
if (!isset($_SESSION["emp_num"])) {   
        header("location:login.php");
    }
?>

<html>

<head>
    <title>OCMS | Home</title>
    <link rel="icon" href="images/feedback.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script>
    // Get all the rows in the table
    var rows = document.querySelectorAll("table tbody tr");

    // Loop through each row and add the serial number
    for (var i = 0; i < rows.length; i++) {
        // Get the first cell in the row
        var cell = rows[i].firstElementChild;

        // Set the value of the cell to the serial number
        cell.textContent = i + 1;
    }
    </script>
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
    }

    .dropdown-item {
        color: #F8F9F9;
    }
    </style>
</head>

<body style="font-family: 'Cantarell', sans-serif;">
    <?php
            $serverName = "NSPCL-AD\SQLEXPRESS";
            $connectionInfo = array(
                "Database" => "Complaint",
                "UID" => "",
                "PWD" => ""
            );           
            $conn = sqlsrv_connect($serverName, $connectionInfo);
            				
                $name = "SELECT emp_name FROM EA_webuser_tstpp WHERE emp_num = ?";
                $params = array($_SESSION['emp_num']);
                $stmt = sqlsrv_query($conn, $name, $params);

                if ($stmt) {
                    // Get the user name from the result set
                    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    $username = $row['emp_name'];

                    // Display the user name
                    
                    echo "<div class='card text-center'>
                    <div class='card-header'>
                    WELCOME , $username to <i><SPAN style='background-color:yellow'> <u>O</u>NLINE <u>C</u>OMPLAINT <u>M</u>ANAGEMENT <u>S</u>YSTEM</SPAN></i>&nbsp;&nbsp;
                    <a href='logout.php'><input type='submit' class='btn btn-success btn-sm' value='LOGOUT'></a>&nbsp;
                    </div>
                    </div>
                    
                    <ul class='nav justify-content-center' style='background-color: #A53DA7;'>                      
                        
                        <li class='nav-item'>
                        <a class='nav-link' href='index.php'>OCMS Home</a>
                        </li>  

                    <li class='nav-item'>
                         <a type='button' class='nav-link dropdown-toggle'  data-bs-toggle='dropdown'>Complaints Register</a>
                        <ul class='dropdown-menu' style='background-color: #7B3575;'>
                                    <li><a class='dropdown-item' href='complaint_reg/Register_comp.php'>Register New Complaint</a></li>
                                    <li><a class='dropdown-item' href='complaint_reg/Check_status.php'>Check Status Of Your Complaint</a></li>                                                   
                        </ul>
                    </li>

                    <li class='nav-item'>
                        <a class='nav-link' href='View_Personal_Info.php'>View Personal Info</a>
                    </li>

                    <li class='nav-item'>
                        <a class='nav-link' href='Modify_Personal_Info.php'>Modify Personal Info</a> 
                     </li>

                    <li class='nav-item'>
                        <a type='button' class='nav-link dropdown-toggle'  data-bs-toggle='dropdown'>Department's Complaints</a>
                        <ul class='dropdown-menu' style='background-color: #7B3575;'>
                        <li><a class='dropdown-item' href='complaint_dep/pending_depart.php'>Check Pending Complaints for your Department</a></li>
                        <li><a class='dropdown-item' href='complaint_dep/complaint_Dept.php'>Check Complaints Attended By Your Department</a></li> 
                        </ul>
                    </li>
   
                    <li class='nav-item'>
                        <a type='button' class='nav-link dropdown-toggle'  data-bs-toggle='dropdown'>Reports</a>
                                <ul class='dropdown-menu' style='background-color: #7B3575;'>
                                    <li><a class='dropdown-item' href='reports/Emp_wise.php'>Employee Wise Reports</a></li>
                                    <li><a class='dropdown-item' href='reports/List_Pend_Comp.php'>List of Pending Complaints with DESCRIPTION</a></li>
                                    <li><a class='dropdown-item' href='reports/Pend_Comp.php'>Pending Complaints For Your Department</a></li>
                                    <li><a class='dropdown-item' href='reports/Summ_Comp.php'>Summary of Complaints</a></li>                                                                    
                                </ul>
                    </li>

                     

                    <li class='nav-item'>
                        <a class='nav-link' href='Change_My_Password.php'>Change my Password</a>
                    </li>                  
            </ul>                
                    ";               
                } else {
                    // Display an error message if the query failed
                    echo "Error fetching user name from database.";
                }

        //update logout session in to database
                if ($conn) {
                    // connection was successful
                    $username = $_SESSION['emp_num'];
                    $sql = "UPDATE login_activity SET logout_time = GETDATE() WHERE emp_num = '$username' ";
                    $params = array($username);
                    $stmt = sqlsrv_query($conn, $sql, $params);
                    if ($stmt) {
                        // logout time was updated successfully
                    } else {
                        // logout time update failed
                    }
                }
			?>
    <br><br>

    <div class="container-fluid">
        <div class="row">
            <div class="col-3">

                <!-- <center><u><span style="text-transform:uppercase;">Last 10 login Details</span></u></center> -->
                <br>
                <!-- First Container -->
                <!-- <?php               
                $serverName = "NSPCL-AD\SQLEXPRESS"; 
                $connectionInfo = array( "Database"=>"Complaint");
                $conn = sqlsrv_connect( $serverName, $connectionInfo);
                $sno = 1;
                if( $conn === false ) {
                    die( print_r( sqlsrv_errors(), true));
               }
               echo "<script>alert('" . $_SESSION['emp_num'] . "');</script>";
               $sql = "SELECT TOP 5 * FROM [Complaint].[dbo].[login_details] ORDER BY login_time DESC";
               $stmt = sqlsrv_query( $conn, $sql );
               $empname ="SELECT emp_name FROM EA_webuser_tstpp WHERE emp_num =''";
               if( $stmt === false) {
                   die( print_r( sqlsrv_errors(), true) );
               }
               
               if( sqlsrv_has_rows( $stmt ) ) {
                
                        echo "<table id='dtBasicExample' class='table table-bordered table-striped table-sm' cellspacing='0' border='2' >";
                        echo "<div class='container-sm'>";
                        echo "<thead class='thead-dark'>";
                        echo "<tr class='th-sm'>";
                        echo "<th>S.NO</th>";
                        echo "<th>Employee No</th>";
                        echo "<th>Login_Time</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "</div>";

                        echo "<tbody>";
                        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
                            $itemname = "SELECT emp_name FROM EA_webuser_tstpp WHERE emp_num='".$row['emp_num']."'";
                            $fetchquery1=sqlsrv_query($conn,$itemname);
                            $row1 = sqlsrv_fetch_array($fetchquery1);
                                echo "<tr>";
                                echo "<td>$sno</td>";
                                echo "<td>" . $row1["emp_name"]. "</td>";
                                echo "<td>" . $row["login_time"]->format('Y-m-d H:i:s'). "</td>";
                     
                       echo "</tr>";
                       $sno++;
                   }
                   echo "</tbody>";
                    echo "</table>";
               } else {
                   echo "0 results";
               }
               
               sqlsrv_free_stmt( $stmt);
               sqlsrv_close( $conn);
               ?> -->
            </div>&nbsp;&nbsp;&nbsp;

            <!-- chart to display in home page -->
            <div class="col-8">
                <!-- <?php include 'get_data.php'?> -->
            </div>
        </div>
    </div>

    <?php include 'footer.php'?>
</body>

</html>
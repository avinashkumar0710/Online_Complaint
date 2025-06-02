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
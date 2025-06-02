

<!DOCTYPE html>
<html>

<head>
    <title>Manage Complaint Type Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php include 'Complaintheader.php';?>
<body>
   
    <?php
    // Step 1: Establish database connection
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

    // Step 2: Fetch user details (plant and department code)
    $userId = $_SESSION['emp_num'];
    //find plant & Department code
    $plant_dptcode = "SELECT plant, dept_code FROM EA_webuser_tstpp WHERE emp_num = '$userId'";
    $result1 = sqlsrv_query($conn, $plant_dptcode);

    if ($result1) {
        // Fetch each row from the result set
        while ($row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
            $plant = $row1['plant'];
            $dept_code = $row1['dept_code'];
            //print $plant;
            //print $dept_code;
        }
    }
    ?>
    <?php

    // Retrieve the maximum compTypeID from compTypeMas table
    $strNewCompID = "SELECT max(compTypeID) as MaxID from compTypeMas";
    $maxResult = sqlsrv_query($conn, $strNewCompID);

    if ($maxResult) {
        $row = sqlsrv_fetch_array($maxResult, SQLSRV_FETCH_ASSOC);
        $maxID = $row['MaxID'];

        if ($maxID === null) {
            $newCompTypeID = 1;
        } else {
            $newCompTypeID = intval($maxID) + 1;
        }

        //echo $newCompTypeID;
    } else {
        echo "Error executing the query: " . sqlsrv_errors();
    }
    // Close the query connection
    sqlsrv_free_stmt($maxResult);
    ?>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['Add'])) {
            // Process the Add action
            $compTypeID = $newCompTypeID;
            $deptID = $_POST['selectDepartment'];
            $compTypeDesc = $_POST['complaint_type_desc'];
            $deptDesc = $_POST['deptName']; // Set the value for deptDesc
            $plant = "$plant"; // Set the value for Plant (replace with appropriate value)

            // Perform the INSERT operation
            $strInsert = "INSERT INTO compTypeMas (compTypeID, deptID, compTypeDesc, deptDesc, Plant) VALUES ('$compTypeID', '$deptID', '$compTypeDesc', '$deptDesc', '$plant')";
            $insertResult = sqlsrv_query($conn, $strInsert);

            if ($insertResult) {
                echo '<script>resetForm();</script>'; // Call JavaScript function to clear input fields
                $successMessage = "Data Inserted successfully.";
                echo '<script>alert("Data Inserted successfully.");</script>';
            
            } else {
                $errorMessage = "Error executing the insert query: " . sqlsrv_errors();
            }
        } 
        
        elseif (isset($_POST['Delete'])) {
            // Process the Delete action
            $compTypeID = $_POST['selectComplaint'];
            // Perform delete query here
            $deleteQuery = "DELETE FROM compTypeMas WHERE compTypeID = $compTypeID";
            $deleteResult = sqlsrv_query($conn, $deleteQuery);

            // Check if the delete was successful
            if ($deleteResult) {
                $successMessage = "Data deleted successfully.";
                echo '<script>alert("Data deleted successfully.");</script>';
            } else {
                $errorMessage = "Error executing the delete query: " . sqlsrv_errors();
            }
        } 
        
        elseif (isset($_POST['Modify'])) {
            // Process the Modify action
            $compTypeID = $_POST['selectComplaint'];
            $compTypeDesc = $_POST['complaint_type_desc'];

            // Perform update query here
            $updateQuery = "UPDATE compTypeMas SET compTypeDesc = '$compTypeDesc' WHERE compTypeID = '$compTypeID'";
            $updateResult = sqlsrv_query($conn, $updateQuery);

            // Check if the update was successful
            if ($updateResult) {
                $successMessage = "Data updated successfully.";
                echo '<script>alert("Data updated successfully.");</script>';
            } else {
                $errorMessage = "Error executing the update query: " . sqlsrv_errors();
            }
        }
    }
    ?>

    <center>
        <div class="container">
            <form method="POST">
                <div class="container_background">
                    <h5><b><u>MANAGE COMPLAINT TYPE MASTER</u></b></h5>
                    <label><i><u>Select the department for which complaint type to be
                                added/modified.</u></i></label><br><br>

                    <label for="selectDepartment">Select Department:</label>
                    <select id="selectDepartment" name="selectDepartment" required>
                        <option value="">Select Department</option>

                        <?php
               $deptDesc = ""; // Initialize $deptDesc outside the loop

               $strSelDept = "SELECT DISTINCT dept_id, DeptName FROM EA_DeptCode_Mas where plant='$plant' order by DeptName";
               $deptResult = sqlsrv_query($conn, $strSelDept);
               if ($deptResult) {
                   while ($row = sqlsrv_fetch_array($deptResult, SQLSRV_FETCH_ASSOC)) {
                       $selected = ($_POST['selectDepartment'] == $row['dept_id']) ? 'selected' : '';
                       $selectedDesc = ($_POST['selectDepartment'] == $row['DeptName']) ? 'selected' : '';
                       //echo '<option value="' . $row['dept_id'] . '" ' . $selected . '>' . $row['DeptName'] . '</option>';
                       echo '<option value="' . $row['dept_id'] . '" data-deptname="' . $row['DeptName'] . '" ' . $selected . '>' . $row['DeptName'] . '</option>';
               
                       if (isset($_POST['Add'])) {
                        if ($_POST['selectDepartment'] == $row['dept_id'] || $_POST['selectDepartment'] == $row['DeptName']) {
                            $deptDesc = $row['DeptName'];
                        }
                    }
                   }
               } else {
                   echo "Error executing the query: " . sqlsrv_errors();
               }

                // Close the department query connection
                sqlsrv_free_stmt($deptResult);
                ?>

                    </select>

                    <!-- Select Complaint Types -->
                    <?php
            if (isset($_POST['selectDepartment'])) {
                $selectedDepartment = $_POST['selectDepartment'];

                // Fetch complaint types for the selected department
                $strSelCompType = "SELECT compTypeDesc, compTypeId FROM compTypeMas WHERE deptID='$selectedDepartment' AND plant='$plant'";
                $compTypeResult = sqlsrv_query($conn, $strSelCompType);

                if ($compTypeResult) {
                    echo '<br><br>';
                    echo '<label for="selectComplaint">Select Complaint Types:</label>';
                    echo '<select id="selectComplaint" name="selectComplaint"   >';
                    echo '<option value="">Select Complaint</option>';

                    while ($row = sqlsrv_fetch_array($compTypeResult, SQLSRV_FETCH_ASSOC)) {
                        echo '<option value="' . $row['compTypeId'] . '">' . $row['compTypeDesc'] . '</option>';
                    }

                    echo '</select>';
                } else {
                    echo "Error executing the complaint types query: " . sqlsrv_errors();
                }

                // Close the complaint types query connection
                sqlsrv_free_stmt($compTypeResult);
            }
            ?>

                    <label for="complaint_type_desc">Complaint Type Description:</label>
                    <input type="text" id="complaint_type_desc" name="complaint_type_desc" required>
                </div><br>
                <!-- JavaScript code to handle the department dropdown -->
                <script>
                // Attach event listener to the selectDepartment dropdown
                document.getElementById("selectDepartment").addEventListener("change", function() {
                    // Submit the form when the selectDepartment value changes
                    this.form.submit();
                });

                // Copy the selected complaint type to the complaint_type_desc input
                document.getElementById("selectComplaint").addEventListener("change", function() {
                    var selectedComplaintType = this.options[this.selectedIndex].text;
                    document.getElementById("complaint_type_desc").value = selectedComplaintType;
                });
                </script>
                <!------------------------------------------------------Select MaxID of compTypeID for add new Complaint Type ----------------------------------------------->
               
                <!---------------------------------------------------For Add New Complaint Type--------------------------------------------------------------------------->

                <input type="submit" value="Add" name="Add" class="border border-success">
                 <!-- <input type="submit" value="Delete" name="Delete" class="border border-danger">  -->
                <input type="submit" value="Modify" name="Modify" class="border border-info">
               
            </form>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('form').submit(function() {
        var selectedDeptName = $('option:selected', 'select[name="selectDepartment"]').data('deptname');
        $('<input>').attr({
            type: 'hidden',
            name: 'deptName',
            value: selectedDeptName
        }).appendTo($(this));
    });
});
</script>
        </div>
    </center>
</body>
<?php include 'footer.php';?>

</html>

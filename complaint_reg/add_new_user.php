<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </script>
    <style>
    th,
    td {
        border: 1px solid #ccc;
        padding: 8px;
    }

    /* Style the heading */
    h3 {
        text-align: center;
        text-decoration: underline;
        margin-bottom: 20px;
    }

    /* Style the complaint status */
    .complaint-status {
        font-weight: bold;
    }

    /* Style the complaint details */
    .complaint-details {
        margin-bottom: 10px;
    }

    #currentDateTime {
        font-family: Arial, sans-serif;
        /* Specify the desired font family */
        font-size: 18px;
        /* Specify the desired font size */
        color: #ff0000;
        /* Specify the desired color */
    }

    #submitBtnContainer {
        text-align: center;
    }

    .input-group {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .input-group text {
        width: 100px;
        margin-right: 10px;
    }

    .designation-note,
    .access-note {
        margin-top: 5px;
        font-size: 14px;
    }

    .btn {
        margin-left: 10px;
    }

    option {
        /* Add your CSS styles here */
        border-radius: 1px;
        font-weight: bold;
        /* Add more styles as needed */
    }

    .container {
        width: 50%;
    }
    </style>
</head>

<body>

    <div class="container">
        <center>
            <h2><u>Add New User Details</h2></u>
        </center><br>

        <form method="POST" action="">
        <div class="input-group">
    <span class="input-group-text" for="selectPlant">Select Your Plant:&nbsp;</span>
    <select class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
            data-bs-display="static" aria-expanded="false" id="selectPlant" name="selectPlant" required onchange="showLocation()">
        <option class="dropdown-item select-option" value="">Please Select Plant</option>
        <!-- PHP code to fetch plant options -->
        <?php
        session_start();
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
        // Fetch plants from the database
        $strSelPlant = "select distinct location, loc_desc FROM [Complaint].[dbo].[emp_mas_sap]";
        $plantResult = sqlsrv_query($conn, $strSelPlant);

        if ($plantResult) {
            while ($row = sqlsrv_fetch_array($plantResult, SQLSRV_FETCH_ASSOC)) {
                $selected = ($_POST['selectPlant'] == $row['location']) ? 'selected' : '';
                echo '<option value="' . $row['location'] . '" ' . $selected . '>' . $row['loc_desc'] . '</option>';
            }
        } else {
            echo "Error executing the query: " . sqlsrv_errors();
        }

        ?>
    </select>
</div>
<br>
<!-- <script>
function showLocation() {
    var selectedPlant = document.getElementById("selectPlant").value;
    if (selectedPlant !== "") {
        // Display the location value in an alert
        alert("Selected Location: " + selectedPlant);
    }
}
</script> -->



            <div class="input-group">
                <span class="input-group-text">Employee ID:</span>
                <input type="text" id="user_id" name="user_id" class="form-control input-sm text-center" required>
            </div>

            <br>

            <div class="input-group">
                <span class="input-group-text">Employee Name:&nbsp;</span>
                <input type="text" id="user_name" name="user_name" class="form-control input-sm text-center" required>
            </div>

            <br>

            <div class="input-group">
                <span class="input-group-text">User Password:</span>
                <input type="password" id="user_password" name="user_password" class="form-control input-sm text-center"
                    required>
            </div>

            <br>

            <div class="input-group">
                <span class="input-group-text">User Designation:</span>
                <input type="text" id="user_designation" name="user_designation"
                    class="form-control input-sm text-center" required>

            </div>
            <p style="text-align: left; font-size: smaller;font-style: italic; font-weight: bold;">(E1, E2, E2A, etc.,
                Designation Code)</p>


            <div class="input-group">
                <span class="input-group-text">User Access:</span>
                <select type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                    data-bs-display="static" aria-expanded="false" id="user_access" name="user_access" required>
                    <option class="dropdown-item select-option" value="">Please Select User Access </option>
                    <option value="0"> For General User</option>
                    <option value="4"> Department Admin</option>
                    <option value="7"> Plant Level Display</option>
                    <!-- <option value="17"> OCMS IT ADMIN</option> -->
                </select>

            </div>
            <p style="text-align: left;font-size: smaller; font-style: italic; font-weight: bold;">Note:<br>
                0 - For General User<br>
                4 - Department Admin<br>
                7 - Plant Level Display<br>
                </p>



                <div class="input-group">
    <span class="input-group-text">User Department Code:&nbsp;</span>
    <select class="btn btn-secondary dropdown-toggle" id="user_department" name="user_department" required>
        <option value="">Select Department</option>
        <?php
        if (isset($_POST["selectPlant"])) {
            $selectedPlant = $_POST["selectPlant"];

            $strSelDept = "SELECT DISTINCT [Dept_id], [DeptName], [Plant] FROM [Complaint].[dbo].[EA_DeptCode_Mas] WHERE plant = '+ selectedPlant'";
            $deptResult = sqlsrv_query($conn, $strSelDept);

            if ($deptResult) {
                while ($row = sqlsrv_fetch_array($deptResult, SQLSRV_FETCH_ASSOC)) {
                    $selected = ($_POST['user_department'] == $row['Dept_id']) ? 'selected' : '';
                    echo '<option value="' . $row['Dept_id'] . '" ' . $selected . '>' . $row['DeptName'] . '</option>';
                }
            } else {
                echo "Error executing the query: " . sqlsrv_errors();
            }
        }
        ?>
    </select>
</div>

<!-- Add this JavaScript code to your HTML -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Get references to the select elements
    var selectPlant = document.getElementById("selectPlant");
    var selectDepartment = document.getElementById("user_department");

    // Attach an event listener to the "Select Your Plant" dropdown
    selectPlant.addEventListener("change", function () {
        var selectedPlant = selectPlant.value;
        
        // Clear the existing options in the "User Department Code" dropdown
        selectDepartment.innerHTML = '<option value="">Select Department</option>';

        if (selectedPlant !== "") {
            // Send an AJAX request to fetch department options
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_departments.php?plant=" + selectedPlant, true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);

                    // Populate the "User Department Code" dropdown with fetched data
                    data.forEach(function (department) {
                        var option = document.createElement("option");
                        option.value = department.Dept_id;
                        option.textContent = department.DeptName;
                        selectDepartment.appendChild(option);
                    });
                }
            };

            xhr.send();
        }
    });
});
</script>


            
            <br>
            <center><button type="submit" name="submit" class="btn btn-success">ADD</button></center>
        </form>
    </div>
    <?php
// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve the form data
    $empNum = $_POST['user_id'];
    $empName = $_POST['user_name'];
    $deptCode = $_POST['user_department'];
    $password = $_POST['user_password'];
    $designationCode = $_POST['user_designation'];
    $status = 'A';
    $access = $_POST['user_access'];
    $plant = $_POST['selectPlant'];

    // Prepare the SQL query
    $sql = "INSERT INTO EA_webuser_tstpp (emp_num, emp_name, dept_code, passwd, designation_code, status, access, Plant) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Set up the parameter values
    $params = array($empNum, $empName, $deptCode, $password, $designationCode, $status, $access, $plant);

    // Execute the query
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Close the connection
    sqlsrv_close($conn);

    // Redirect or display success message
    // You can redirect to another page or display a success message here
    echo '<script>alert("Data inserted successfully.");</script>';
}
?>
</body>

</html>
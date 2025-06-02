<!DOCTYPE html>
<html>

<head>
    <title>Modify User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
    .input-group {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .input-group text {
        width: 100px;
        margin-right: 10px;
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
    <script>
    // Lock all inputs on page load
    document.addEventListener("DOMContentLoaded", function() {
        lockInputs();
    });

    function lockInputs() {
        // Get all input elements
        var inputs = document.getElementsByTagName("input");

        // Disable all input elements
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].disabled = true;
        }
    }

    // function toggleInputs() {
    //     // Get all input elements
    //     var inputs = document.getElementsByTagName("input");

    //     // Toggle input element's disabled state
    //     for (var i = 0; i < inputs.length; i++) {
    //         inputs[i].disabled = !inputs[i].disabled;
    //     }
    // }

    function toggleInputs() {
        var inputs = document.getElementsByTagName("input");
        var modifyButton = document.getElementById('modifyButton');

        // Toggle input elements' disabled state
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].disabled = !inputs[i].disabled;
        }

        // Toggle MODIFY button's disabled state
        modifyButton.disabled = !modifyButton.disabled;
    }
</script>
</head>
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
$emp_num = $_GET['emp_num'];
//echo "Emp No: " . $emp_num;

$plant_dptcode = "SELECT 
w.[emp_num],
w.[passwd],
w.[emp_name],
w.[designation_code],
w.[dept_code],
w.[access],
w.[status],
w.[Plant],
d.[Dept_id],
d.[DeptName],
d.[Plant] AS Dept_Plant
FROM 
[EA_webuser_tstpp] w
INNER JOIN 
[Complaint].[dbo].[EA_DeptCode_Mas] d ON w.dept_code = d.Dept_id
WHERE 
w.emp_num = '$emp_num';
";
$result1 = sqlsrv_query($conn, $plant_dptcode);

if ($result1) {
    if (sqlsrv_has_rows($result1)) {
        // Fetch each row from the result set
        while ($row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
            $plant = $row1['Plant'];
            $emp_name = $row1['emp_name'];
            $passwd = $row1['passwd'];
            $designation_code = $row1['designation_code'];
            $access = $row1['access'];
            $dept_code = $row1['dept_code'];
            $DeptName = $row1['DeptName'];
            //print $DeptName;
            //print $dept_code;
        }
    } else {
        // Record not found, display a JavaScript alert
        
        
    }
} else {
    // Handle the database error
    echo "Error executing the query: " . sqlsrv_errors();
}
?>

            <body>
            <?php if (sqlsrv_has_rows($result1)) { ?>
<div class="container">
    <center><p style="font-size: smaller;font-style: italic; font-weight: bold; background-color: yellowgreen;">NOTE : To modify/Update details first Unlock the form by click Lock/Unlock Button</p> 
        <h5><u>User Details for&nbsp;<?php echo $emp_name?></u></h5>
    </center><br>

    <!-- PHP code to fetch employee details to update -->
    <form method="POST" id="userForm">
    <div class="input-group">
        <span class="input-group-text">Select to change your Plant:&nbsp;</span>
        <select class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static"
                aria-expanded="false" id="selectPlant" name="selectPlant" required onchange="updatePlantInput()">
            <option class="dropdown-item select-option" value="">Please Select Plant</option>
            <?php
            // Fetch plant options
            $query = "SELECT DISTINCT location, loc_desc FROM [Complaint].[dbo].[emp_mas_sap]";
            $result = sqlsrv_query($conn, $query);
            if ($result) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $selected = ($row['location'] == $Plant) ? "selected" : "";
                    echo '<option value="' . $row['location'] . '" ' . $selected . '>' . $row['loc_desc'] . '</option>';
                }
            }
            ?>
        </select>
    </div><br>

    <div class="input-group">
        <span class="input-group-text">Employee ID:</span>
        <input type="text" id="user_id" name="user_id" class="form-control input-sm text-center"
            value="<?php echo $emp_num; ?>" required>
    </div>

    <div class="input-group">
        <span class="input-group-text">Employee Name:</span>
        <input type="text" id="emp_name" name="emp_name" class="form-control input-sm text-center"
            value="<?php echo $emp_name; ?>" required>
    </div>

    <div class="input-group">
        <span class="input-group-text">Current Password:</span>
        <input type="password" id="user_password" name="user_password" class="form-control input-sm text-center"
            value="<?php echo $passwd; ?>" required>&nbsp;
        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
            <img src="images/eye-slash.svg" alt="Toggle Password Visibility" width="20" height="20">
        </button>&nbsp;
        <p style="font-size: smaller;font-style: italic; font-weight: bold;">(Click here to view your password)</p>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("user_password");
            var toggleButton = document.getElementById("togglePassword");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleButton.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                passwordInput.type = "password";
                toggleButton.innerHTML = '<i class="bi bi-eye-slash"></i>';
            }
        }
    </script>

    <div class="input-group">
        <span class="input-group-text">Designation Code:</span>
        <input type="text" id="designation_code" name="designation_code" class="form-control input-sm text-center"
            value="<?php echo $designation_code; ?>" required>
    </div>
    <p style="text-align: left; font-size: smaller;font-style: italic; font-weight: bold;">(E1, E2, E2A, etc.,
        Designation Code)</p>

    <div class="input-group">
        <span class="input-group-text">User Access:</span>
        <input type="text" id="access" name="access" class="form-control input-sm text-center"
            value="<?php echo $access; ?>" required>
    </div>
    <p style="text-align: left;font-size: smaller; font-style: italic; font-weight: bold;">Note:<br>
        0 - For General User<br>
        4 - Department Admin<br>
        7 - Plant Level Display<br>
        17 - OCMS IT ADMIN</p>

    <div class="input-group">
        <span class="input-group-text">Dept:</span>
        <input type="text" id="DeptName" name="DeptName" class="form-control input-sm text-center"
            value="<?php echo $DeptName; ?>" required>
            <select class="btn btn-secondary dropdown-toggle" id="user_department" name="user_department" required>
            <option value="">Modify Plant</option>
            <?php
            // PHP will dynamically populate options based on AJAX response
            ?>
        </select>
        <br>
    </div>
    <!-- <center>
        <button type="button" class="btn btn-primary" onclick="toggleInputs()">Lock/Unlock</button>
        <button type="submit" name="submit" class="btn btn-success">MODIFY</button>
    </center> -->

   
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
            selectDepartment.innerHTML = '<option value="">Modify Plant</option>';

            if (selectedPlant !== "") {
                // Send an AJAX request to fetch department options
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "fetch_departments.php?plant=" + encodeURIComponent(selectedPlant), true);

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

    

    <center>
    <button type="button" class="btn btn-primary" onclick="toggleInputs()">Lock/Unlock</button>
    <button type="submit" name="submit" id="modifyButton" class="btn btn-success" disabled>MODIFY</button>
</center>


</form>


<?php
// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve the form data
    $Plant = $_POST['selectPlant'];
    $emp_num = $_POST['user_id'];
    $emp_name = $_POST['emp_name'];
    $passwd = $_POST['user_password'];
    $designation_code = $_POST['designation_code'];
    $access = $_POST['access'];
    //$dept_code = $_POST['dept_code'];
    $dept_code = $_POST['user_department'];

    // Prepare the update query
    $query = "UPDATE EA_webuser_tstpp SET emp_name = ?, designation_code = ?, dept_code = ?, Plant = ?, passwd = ?, access = ? WHERE emp_num = ?";
    $params = array($emp_name, $designation_code, $dept_code, $Plant, $passwd, $access, $emp_num);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Close the connection
    sqlsrv_close($conn);

    // Set a success message
    $success_message = "Data Updated successfully.";
    
    // Redirect using JavaScript after a delay
    
}

?>
</div>
<?php } else { ?>
   <center> <p>This Employee ID: <b><?php echo $emp_num; ?></b> is not Registered yet. Click Add New button to add Employee.</p></center>
    <?php } ?>
            </body>
</html>
<?php include 'Complaintheader.php';?>
<?php 
//database connection
session_start();
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaint Register</title>

    <style>
    .change-password-container {
        width: 800px;
        padding: 20px;
        background-color: #f2f2f2;
        border-radius: 5px;
    }

    .change-password-container h2 {
        text-align: center;
    }

    .change-password-container label {
        font-weight: bold;
    }

    .change-password-container input[type="text"],
    .change-password-container input[type="password"] {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .change-password-container .buttons {
        text-align: center;
    }

    .change-password-container .buttons input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    input {
        height: 33px;
        text-align: center;
    }
    </style>
    <script>
    function resetForm() {
        document.getElementById("passwordForm").reset();
    }
</script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <center>
        <div class="change-password-container">
            <!-- <h2>Complaint Register</h2>
            <u><p>Select the department for which complaint is to be registered.</p></u> -->
        
            <label for="userId">Select Department:</label>
<select id="study" class="form-select" name="selecteditem" onchange="enableFormFields()">
<?php
// Retrieve the plant value from the cookie
$plantQuery = "SELECT Plant, emp_num, dept_code FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$_SESSION[emp_num]'";
$plantResult = sqlsrv_query($conn, $plantQuery);
$userId = $_SESSION['emp_num'];   //store the userid which is passed in the session variable

if ($plantResult) {
    $row = sqlsrv_fetch_array($plantResult, SQLSRV_FETCH_ASSOC);
    $plant = $row['Plant'];
    $dept_code = $row['dept_code'];
    print $dept_code;  //here dept code is coming

    $deptQuery = "SELECT DISTINCT deptID, deptDesc FROM compTypeMas WHERE plant = '$plant'";
    $deptResult = sqlsrv_query($conn, $deptQuery);

    if ($deptResult === false) {
        die(print_r(sqlsrv_errors()));
    }

    echo "<option value=''>Select Department</option>";

    $selectedDeptID = isset($_POST['selecteditem']) ? $_POST['selecteditem'] : '';

    while ($row = sqlsrv_fetch_array($deptResult, SQLSRV_FETCH_ASSOC)) {
        $deptID = $row['deptID'];
        $deptDesc = $row['deptDesc'];
        $selected = $deptDesc === $selectedDeptID ? "selected" : "";
        echo "<option value='$deptDesc' $selected>$deptDesc</option>";
    }

    sqlsrv_free_stmt($deptResult);
}

sqlsrv_free_stmt($plantResult);

$findDeprt = "SELECT Dept_id ,DeptName 
FROM EA_DeptCode_Mas 
WHERE Dept_id = '$dept_code' and Plant='$plant'";
$params = array($_SESSION['emp_num']);
$result1 = sqlsrv_query($conn, $findDeprt, $params);

// Check if the query was successful
if ($result1 === false) {
    die(print_r(sqlsrv_errors(), true));
}

$dept1 = ""; // Initialize the variable

if (sqlsrv_has_rows($result1)) {
    $row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC);
    $dept1 = $row1['DeptName'];
}

echo $dept1;

?>
</select>
    
    <hr>
    <form action="Register_com_process.php" method="POST" id="passwordForm">
    <p>Please fill the fields to register your complaint (Note: * marked fields are required)</p>
    
    
    <label for="CompDate">Complaint Date:</label>
    <input type="date" name="CompDate" id="CompDate" value="<?php echo date('Y-m-d'); ?>" required readonly disabled><br>

    <label for="CompUserID">UserID:</label>
    <input type="text" name="CompUserID" id="CompUserID" value="<?php echo isset($userId) ? $userId : ''; ?>" required readonly disabled><br>

    <label for="userdept">User Department:</label>
    <input type="text" name="userdept" id="userdept" value="<?php echo isset($dept_code) ? $dept1 . ' (' . $dept_code . ')' : ''; ?>" required readonly disabled><br>

    <label for="comdept">Complaint for Department:</label>
    <input type="text" name="comdept" id="comdept" value="" required readonly disabled><br>

    <label for="comTypeDesc">Complaint Type:</label>
    <select type="text" name="comTypeDesc" id="comTypeDesc" class="form-select" required disabled></select>
    <br>
    <script>
        const select = document.getElementById("study");
        const comdeptInput = document.getElementById("comdept");
        const comTypeDescSelect = document.getElementById("comTypeDesc");

        select.addEventListener("change", () => {
            const selectedValue = select.options[select.selectedIndex].value;
            comdeptInput.value = selectedValue;
            comTypeDescSelect.value = ""; // Set it to an empty string
            complaintImageInput.disabled = false; // Enable the image upload field

        // Make an AJAX request to fetch the complaint types based on the selected comdept
        $.ajax({
            url: "fetch_complaint_types.php",
            type: "POST",
            data: { comdept: selectedValue },
                success: function(response) {
                    // Update the "Complaint Type" dropdown with the retrieved complaint types
                    comTypeDescSelect.innerHTML = response;
                },
                error: function(xhr, status, error) { 
                    console.error(xhr.responseText);
                }
            });
        });
    </script>
   

    <label for="Description">Complaint Description:</label><br>
    <input type="text" name="Description" id="Description" required disabled><br>

    <label for="contactNo">Contact No:</label>
    <input type="text" name="contactNo" id="contactNo"  value="<?php ?>"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"required disabled><br>

    <label for="Location">Location:</label>
    <input type="text" name="Location" id="Location"  required disabled><br>

    <label for="complaintImage">Upload Image:</label>
    <input type="file" name="complaintImage" id="complaintImage" accept="image/*" required disabled><br><br>


    <!-- submit data -->

    <!-- <div class="buttons">
    <input type="button" value="Reset" onclick="resetForm()">
    </div> -->
    
    <div class="buttons">
        <input type="button" class="btn btn-primary" name ="Reset" value="Reset" onclick="resetForm()" disabled>
        <input type="submit" class="btn btn-success" name ="Submit" value="Submit" disabled>
    </div>
</form>


    <script>
    function enableFormFields() {
        var departmentSelect = document.getElementById("study");
        var form = document.getElementById("passwordForm");
        
        if (departmentSelect.value !== "") {
        // Enable form fields
        for (var i = 0; i < form.elements.length; i++) {
            form.elements[i].disabled = false;
        }
        } else {
        // Disable form fields
        for (var i = 0; i < form.elements.length; i++) {
            form.elements[i].disabled = true;
        }
        }
    }
    </script>
        </div>
    </center>
</body>
</html><br>
<?php include 'footer.php';?>

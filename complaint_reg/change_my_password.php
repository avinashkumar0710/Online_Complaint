<?php include 'Complaintheader.php';?>

<?php
$userId = $_SESSION['emp_num'];
// Fetch the user's name from the database
$sql = "SELECT emp_name FROM EA_webuser_tstpp WHERE emp_num = '$userId'";
$result = sqlsrv_query($conn, $sql);
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
$empname = $row['emp_name'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if the old password matches the confirm password
    if ($newPassword === $confirmPassword) {
        // Prepare the SQL statement to check if the old password is correct
        $checkSql = "SELECT emp_num FROM EA_webuser_tstpp WHERE emp_num = '$userId'";
        $checkResult = sqlsrv_query($conn, $checkSql);
        if ($checkResult === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // If the old password is correct, update the password
        if (sqlsrv_has_rows($checkResult)) {
            // Prepare the SQL statement to update the password
            $updateSql = "UPDATE EA_webuser_tstpp SET passwd = '$newPassword' WHERE emp_num = '$userId'";
            $updateResult = sqlsrv_query($conn, $updateSql);
            if ($updateResult === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // Password updated successfully
            echo "Password updated successfully.";
        } else {
            // Old password is incorrect, display an error message or take appropriate action
            echo "Old password is incorrect.";
        }

        // Clean up resources
        sqlsrv_free_stmt($checkResult);
        if (isset($updateResult)) {
            sqlsrv_free_stmt($updateResult);
        }
    } else {
        // Old password and confirm password do not match
        echo "Old password and confirm password do not match.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    
    <style>
        .change-password-container {
            width: 400px;
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
    </style>
   <script>
    function resetPasswordForm() {
        document.getElementById("passwordForm").reset();
    }
</script>
</head>
    
<body><center>
    <div class="change-password-container">
        <h2>Change Password</h2>
        <form action="" method="POST" id="passwordForm">
            <label for="userId">User ID:</label>
            <input type="text" name="userId" id="userId" value="<?php echo isset($userId) ? $userId : '';?>" readonly>

            <label for="empname">Your Name:</label>
            <input type="text" name="empname" id="empname" value="<?php echo isset($empname) ? $empname : ''; ?>" readonly>

            <label for="oldPassword">Old Password:</label>
            <input type="password" name="oldPassword" id="oldPassword" required>

            <label for="newPassword">New Password:</label>
            <input type="password" name="newPassword" id="newPassword" required>

            <label for="confirmPassword">Confirm New Password:</label>
            <input type="password" name="confirmPassword" id="confirmPassword" required>

            <div class="buttons">
                <input type="submit" name="changePassword" value="Update Password">
                <input type="submit" name="resetForm" value="Reset" onclick="resetPasswordForm()">
            </div>
        </form>
    </div></center>
</body>
</html>
<?php include 'footer.php';?>

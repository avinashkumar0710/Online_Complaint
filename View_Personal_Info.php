<?php include 'index.php';?>
<?php
$userID = $_SESSION["emp_num"];
$imageURL = "http://192.168.100.9:8080/nspclBirthday/empimages/" . $userID . ".jpg";

$sql = "SELECT * FROM emp_mas WHERE empno = '$userID'";
$result = sqlsrv_query($conn, $sql);
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
$empno=$row['empno']  ?? '';
$empname = $row['emp_name']  ?? '';
$userDesig = $row['design']  ?? '';
$userDeptDesc = $row['dept'] ?? '';
$usermob1 = $row['mobile1'] ?? '';
$usermob2 = $row['mobile2'] ?? '';
$userdob = $row['dob'] ?? '';
$userintoff = $row['inter_o'] ?? '';
$userintres = $row['inter_r'] ?? '';
$usermail = $row['email_id'] ?? '';
$useroffice = $row['office_no'] ?? '';
$userstdcode = $row['std_code'] ?? '';
$useraddr = $row['address'] ?? '';
$userstat = $row['status'] ?? '';
$userresno = $row['res_no'] ?? '';
$userstdres = $row['std_res'] ?? '';
$userplant = $row['plant'] ?? '';
$userorg = $row['org'] ?? '';
$userqtr_type = $row['qtr_type'] ?? '';
//$userblock_no = $row['block_no'];
//$userqtr_no = $row['qtr_no'];
$usergrade = $row['grade'] ?? '';

// echo "userid: ";
//     var_dump($userID);
 ?>
<html>

<head>
    <title>View Personal Info</title>
    <script>
    function toggleInputs() {
        var checkbox = document.getElementById("checkboxButton");
        var inputs = document.getElementsByTagName("input");

        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i] !== checkbox) {
                inputs[i].disabled = !checkbox.checked;
            }
        }
    }
    </script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Cantarell&display=swap');

    .change-password-container {
        width: 1300px;
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

    .form-container {
        display: flex;
        justify-content: space-between;
    }

    .form-section {
        width: 50%;
        padding: 10px;
        margin-bottom: 0px;
    }

    input {
        height: 30px;
        text-align: center;
    }
    </style>
</head>
<center>

    <body>
        <div class="change-password-container">
            <h2>VIEW PERSONAL DETAILS</h2>
            <form action="Personal_info_update.php" method="POST">
                <div class="form-section" style="display: flex; justify-content: center; align-items: center;">
                    <center>
                        <img align="left" height="115px" width="115px" src="<?php echo $imageURL; ?>"
                            style="border-radius: 50%;" />
                    </center>
                </div>
                <div class="form-container">
                    <div class="form-section">

                        <label for="userId">Employee No:</label>
                        <input type="text" name="empno" id="empno" value="<?php echo isset($empno) ? $empno : ''; ?>"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57" disabled
                            placeholder="No Data valiable in database">


                        <label for="empname">Your Name:</label>
                        <input type="text" name="emp_name" id="emp_name"
                            value="<?php echo isset($empname) ? $empname : ''; ?>" disabled
                            placeholder="No Data valiable in database">

                        <label for="dob">DOB:</label>
                        <input type="text" name="dob" id="dob"
                            <?php echo isset($row['dob']) ? "value='" . $row['dob']->format('Y-m-d') . "'" : "placeholder='No Data valiable in database'"; ?>
                            disabled>



                        <label for="Department">Department:</label>
                        <input type="text" name="dept" id="dept"
                            value="<?php echo isset($userDeptDesc) ? $userDeptDesc : ''; ?>" disabled placeholder="No Data valiable in database">

                        <label for="Grade">Grade:</label>
                        <input type="text" name="grade" id="grade"
                            value="<?php echo isset($usergrade) ? $usergrade : ''; ?>" disabled placeholder="No Data valiable in database">

                        <label for="Designation">Designation:</label>
                        <input type="text" name="design" id="design"
                            value="<?php echo isset($userDesig) ? $userDesig : ''; ?>" disabled placeholder="No Data valiable in database">

                        <label for="Mobile1">Mobile 1:</label>
                        <input type="text" name="mobile1" id="mobile1"
                            value="<?php echo isset($usermob1) ? $usermob1 : ''; ?>"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57" disabled placeholder="No Data valiable in database">

                        <label for="Mobile2">Mobile 2:</label>
                        <input type="text" name="mobile2" id="mobile2"
                            value="<?php echo isset($usermob2) ? $usermob2 : ''; ?>"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57" disabled placeholder="No Data valiable in database">

                        <label for="IntercomOffice">Intercom Office:</label>
                        <input type="text" name="inter_o" id="inter_o"
                            value="<?php echo isset($userintoff) ? $userintoff : ''; ?>"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57" disabled placeholder="No Data valiable in database">
                    </div>

                    <div class="form-section">
                        <label for="IntercomResident">Intercom Resident:</label>
                        <input type="text" name="inter_r" id="inter_r"
                            value="<?php echo isset($userintres) ? $userintres : ''; ?>"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57" disabled placeholder="No Data valiable in database">


                        <label for="OfficeNO" style="display: inline-block; margin-right: 5px;">Office NO:</label>
                        <div class="input-group">
                            Std Code :&nbsp;&nbsp;<input type="text" name="office_no" id="office_no"
                                value="<?php echo isset($useroffice) ? $useroffice : ''; ?>" disabled
                                style="width: 30%;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="No Data valiable in database">
                            &nbsp;&nbsp;- NO :&nbsp;&nbsp;<input type="text" name="std_code" id="std_code"
                                value="<?php echo isset($userstdcode) ? $userstdcode : ''; ?>" disabled
                                style="width: 30%;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="No Data valiable in database">
                        </div>



                        <label for="ResidentNO" style="display: inline-block; margin-right: 5px;">Resident NO:</label>
                        <div class="input-group">
                            Std Code :&nbsp;&nbsp;<input type="text" name="std_res" id="std_res"
                                value="<?php echo isset($userstdres) ? $userstdres : ''; ?>" disabled
                                style="width: 30%;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="No Data valiable in database">
                            &nbsp;&nbsp;- NO :&nbsp;&nbsp;<input type="text" name="res_no" id="res_no"
                                value="<?php echo isset($userresno) ? $userresno : ''; ?>" disabled style="width: 30%;"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="No Data valiable in database">
                        </div>


                        <label for="EmailID">Email ID:</label>
                        <input type="text" name="email_id" id="email_id"
                            value="<?php echo isset($usermail) ? $usermail : ''; ?>" disabled placeholder="No Data valiable in database">

                        <label for="Address">Address:</label>
                        <input type="text" name="address" id="address"
                            value="<?php echo isset($useraddr) ? $useraddr : ''; ?>" disabled placeholder="No Data valiable in database">

                        <label for="TownshipDetails">Township Quarter Detail:</label>
                        <input type="text" name="qtr_type" id="qtr_type"
                            value="<?php echo isset($userqtr_type) ? $userqtr_type : ''; ?>" disabled placeholder="No Data valiable in database">

                        <label for="PlantLocation">Plant Location:</label>
                        <input type="text" name="plant" id="plant"
                            value="<?php echo isset($userplant) ? $userplant : ''; ?>" disabled placeholder="No Data valiable in database">

                        <label for="Organisation">Organisation:</label>
                        <input type="text" name="org" id="org" value="<?php echo isset($userorg) ? $userorg : ''; ?>"
                            disabled placeholder="No Data valiable in database">

                        <label for="EmployeeStatus">Employee status:</label>
                        <input type="text" name="status" id="status"
                            value="<?php echo isset($userstat) ? $userstat : ''; ?>" disabled placeholder="No Data valiable in database">



                    </div>
                    <div class="form-section">
                        <div class="input-group" style="display: flex; justify-content: center; align-items: center;">
                            <input type="checkbox" id="checkboxButton" onchange="toggleInputs()">
                            <label for="checkboxButton"><i>(*click checkbox before modify*)</i></label>
                        </div>
                        <button type="submit">Update</button>
                    </div>
                </div>
        </div>

        </form>

    </body>

</html>
<?php include 'footer.php';?>
<!DOCTYPE html>
<html>

<head>
    <title>Manage Complaint Type Master</title>

    <style>
    .modal {
        display: none;
        position: fixed;
        bottom: 0;
        left: 50%;
        width: 50%;
        height: 100%;
        z-index: 9999;
        transform: translateY(100%);
        transition: transform 0.3s ease-out;
        background-color: #DEEEA2;
        padding: 40px 400px 0px 400px;

    }

    .modal.active {
        transform: translateY(0);
    }

    .modal-content {
        height: 100%;
        border-radius: 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 20px;
        position: relative;
        margin: 10 auto;
        animation: modal-content-animation 0.7s;


    }

    @keyframes modal-content-animation {
        from {
            transform: translateY(100%);
        }

        to {
            transform: translateY(0);
        }
    }
    </style>
</head>
<?php include 'Complaintheader.php';?>
<?php
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

<body>
    <center>
        <div class="container">
            <form method="POST" action="" id="userForm">
                <div class="container_background">
                    <h5><b><u>MANAGE USER MASTER</u></b></h5><br>
                    <label><i><u>Enter the UserID which need to be added/modified.</u></i></label><br><br>
                    <label for="emp_num">User ID:</label>
                    <input type="text" id="emp_num" name="emp_num" minlength="6" required>
                    <br><br>
                    <script>
                    document.getElementById("userForm").addEventListener("submit", function(event) {
                        event.preventDefault(); // Prevent form submission

                        // Get the emp_no value from the input field
                        var emp_num = document.getElementById("emp_num").value;

                        // Open the modal popup
                        openModal('../complaint_reg/modify_user.php?emp_num=' + emp_num);


                    });
                    </script>

                    <!-------for modify any exist user---------->
                   
                        <button type="submit" name="submit" class="btn btn-success">Submit</button> <a href="#" onclick="openModal('../complaint_reg/modify_user.php?emp_num=<?php echo $emp_num; ?>')">
                    </a>


                    <!-------for add new user---------->
                    <a href="#" onclick="openModal('../complaint_reg/add_new_user.php')">
                        <button type="button" name="add_new" class="btn btn-info">Add New</button>
                    </a>

                </div>
            </form>

    </center>
    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h2></h2>
            <iframe id="modal-iframe" frameborder="0" width="100%" height="100%"></iframe>
            <center><button class="modal-close" onclick="closeModal()" style="width:50%">
                    <img src="images/icons8-close.gif" alt="Close">&nbsp;&nbsp;CLOSE
                </button></center>
        </div>
    </div>

    <script>
    function openModal(url) {
        var modal = document.getElementById("modal");
        var iframe = document.getElementById("modal-iframe");

        // Set the iframe source to the specified URL
        iframe.src = url;

        // Display the modal
        modal.style.display = "block";

        // Add the active class to trigger the transition
        setTimeout(function() {
            modal.classList.add("active");
        }, 0);
    }

    function closeModal() {
        var modal = document.getElementById("modal");
        modal.classList.remove("active");
        setTimeout(function() {
            modal.style.display = "none";
        }, 500);
    }

    document.querySelector(".modal-close").addEventListener("click", closeModal);
    </script>
    </div>
</body>

<?php include 'footer.php';?>
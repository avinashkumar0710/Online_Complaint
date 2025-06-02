<html>

<head>
    <title>Welcome to OCMS | Login</title>
    <link rel="icon" href="images/feedback.png">
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<style>

body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    padding: 0;
    background-color: #C4758B;
    font-family: 'Lexend', sans-serif;
}

.container {
    display: flex;
    border-radius: 15px;
    overflow: hidden;
    /* Prevent line overflow */
    background-color: #ffffff;
    height: 600px;
    box-shadow: 0px 0px 20px 5px #E8F2EA;
    width: 1400px;
}

.image-section {
    flex: 1;
    padding: 20px;
    border-right: 1px solid #ccc;
    /* Add a border on the right side */
}

.login-section {
    flex: 1;
    padding: 20px;
    background-color: white;
    border-radius: 0px 15px 15px 0px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
}

.img-fluid {
    max-width: 80%;
    height: auto;
}

.note {
    text-align: left;
    font-weight: 100;
}

.slider-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 600px;
    width:100%;
    overflow: hidden;
}

.slider {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slider img {
    width: 100%; /* Make images cover full width */
        height: 100%;
        object-fit: cover; /* Maintain aspect ratio */
}
</style>

<center>

    <body onload="display_ct();">
        <div class="container">
            <div class="row">
                <!-- Left Section: Image Display -->
                <div class="col-6" style="margin: 0; padding: 0;">
                    <div class="slider-container">
                        <div class="slider" style="height:600px;">
                            <img src="images/IT dept.gif" alt="Image 5">
                            <img src="images/c&i.png" alt="Image 1">
                            <img src="images/civil.gif" alt="Image 2">
                            <img src="images/electrical.gif" alt="Image 3">
                            <img src="images/hr-g.gif" alt="Image 4">
                            
                            
                            <!-- Add more images as needed -->
                        </div>
                    </div>
                </div>
                <script>
                const slider = document.querySelector(".slider");
                let count = 0;

                setInterval(() => {
                    count++;
                    if (count >= slider.children.length) {
                        count = 0;
                    }
                    slider.style.transform = `translateX(-${count * 100}%)`;
                }, 4000); // Change slide every 3 seconds
                </script>

                <!-- Right Section: Login Form -->
                <div class="col-6" style="background-color:#4253b1; margin: 0; padding: 0;">
                    <div style="width:600px; background-color:#4253b1;"><br>

                        <legend style="font-size:40px;color:#ffffff;">LOGIN TO OCMS</legend><br><br>
                        <form action="loginprocess.php" method="POST" id="form_id" style="width:450px;">
                            <div class="input-group input-group-lg" style="width:450px; color:#B1B6E7;">
                                <input type="text" class="form-control" required="" name="emp_num"
                                    placeholder=" Username" aria-label="Large"
                                    aria-describedby="inputGroup-sizing-sm">

                            </div><br>

                            <div class="input-group input-group-lg" style="width:450px; color:#B1B6E7">
                                <input type="password" class="form-control" name="passwd"
                                    placeholder=" Password" aria-label="Large"
                                    aria-describedby="inputGroup-sizing-sm" required="">

                            </div><br>
                            <?php
        if (isset($_SESSION['login_error']) && $_SESSION['login_error']) {
            echo '<p style="color: red;">Invalid username or password. Please try again.</p>';
            $_SESSION['login_error'] = false; // Reset the login error session variable
        }
        ?>
                           
                            <input type="submit" class="btn btn-success btn-lg" value="LOGIN"
                                name="sub">
                                <input type="button" class="btn btn-info btn-lg" onclick="resetForm()"
                                value="RESET"><br><br>


                            <h6 style="color:#ffffff"><u>(Please Login to Proceed)</u></h6>

                            <div class="note">

                                <b><br>
                                    <center><button class="btn btn-info"
                                            style="border-radius:20px;">Instructions:</button></center><br>
                                    <p style="color:#ffffff"> 1.) Default Username & password is
                                        Employee Number.<br>
                                        <br>
                                    </p>
                                </b>

                            </div>
                            <script>
                            function resetForm() {
                                document.getElementById("form_id").reset();
                            }
                            </script>
                           
                           
                        </form>
                        
                        <!-- Your JavaScript code to show the modal -->
  
                    </div>
                </div>
            </div>
        </div><br>
        
        <img src="images/nspcl_logo1.png" alt="NSPCL" style="height:96px;width:150px;"><br>
        <span id='ct' style="background-color:yellow"></span>
        <script type="text/javascript">
        function display_c() {
            var refresh = 1000; // Refresh rate in milli seconds
            mytime = setTimeout('display_ct()', refresh)
        }

        function display_ct() {
            var x = new Date();
            document.getElementById('ct').innerHTML = x;
            display_c();
        }
        display_c(); // added to call the display_c function on page load
        </script>
   <?php
// Database connection settings
$serverName = "192.168.100.240";
$connectionOptions = array(
    "Database" => "complaint",
    "UID" => "sa",
    "PWD" => "Intranet@123"
);

// Get the visitor's IP address
$visitor_ip = $_SERVER['REMOTE_ADDR'];

// Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Check if the IP address is already in the database
$checkQuery = "SELECT COUNT(*) AS Count FROM unique_visitors WHERE ip_address = ?";
$params = array($visitor_ip);
$checkResult = sqlsrv_query($conn, $checkQuery, $params);
$checkRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);

if ($checkRow['Count'] == 0) {
    // If the IP address is not in the database, insert it
    $insertQuery = "INSERT INTO unique_visitors (ip_address, visit_date) VALUES (?, GETDATE())";
    $insertResult = sqlsrv_query($conn, $insertQuery, $params);
}

// Get the total number of unique visitors
$totalVisitorsQuery = "SELECT COUNT(DISTINCT ip_address) AS TotalVisitors FROM unique_visitors";
$totalVisitorsResult = sqlsrv_query($conn, $totalVisitorsQuery);
$totalVisitorsRow = sqlsrv_fetch_array($totalVisitorsResult, SQLSRV_FETCH_ASSOC);
$totalVisitors = $totalVisitorsRow['TotalVisitors'];

// Close the database connection
sqlsrv_close($conn);
?>
    </body>
    <p>Total Visitors: <?php echo $totalVisitors; ?></p>

</center>
    <?php include 'footer.php';?>
</html>
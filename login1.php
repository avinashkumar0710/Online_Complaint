<html>

<head>
    <title>Welcome to OCMS | Login</title>
    <link rel="icon" href="images/feedback.png">   
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
 
</head>
<style>
.img-fluid {
    border-radius: 15px;
}

body {

    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
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
    width: 1200px;
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
    overflow: hidden;
}

.slider {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slider img {
    width: 100%;
    height: auto;
}
</style>

<center>

    <body>
        <div class="container">
            <div class="row">
                <!-- Left Section: Image Display -->
                <div class="col-6">
                    <div class="slider-container">
                        <div class="slider" style="height:600px;">
                            <img src="images/bhilai pp2.jpg" alt="Image 1">
                            <img src="images/bhilai pp3.jpg" alt="Image 2">
                            <img src="images/durgapur.jpg" alt="Image 3">
                            <img src="images/Rourkela.jpg" alt="Image 4">
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
                <div class="col-6" style="background-color:#4253b1;">
                    <div style="width:600px; background-color:#4253b1;"><br>

                        <legend style="font-size:40px;color:#ffffff;">LOGIN TO OCMS</legend><br><br>
                        <form action="loginprocess.php" method="POST" id="form_id" style="width:450px;">
                            <div class="input-group input-group-lg" style="width:450px; color:#B1B6E7;">
                                <input type="text" class="form-control" required="" name="emp_num"
                                    placeholder="Please fill Username" aria-label="Large"
                                    aria-describedby="inputGroup-sizing-sm">

                            </div><br>

                            <div class="input-group input-group-lg" style="width:450px; color:#B1B6E7">
                                <input type="password" class="form-control" name="passwd"
                                    placeholder="Please fill Password" aria-label="Large"
                                    aria-describedby="inputGroup-sizing-sm" required="">

                            </div><br>

                            <input type="button" class="btn btn-info btn-lg btn-block" onclick="resetForm()"
                                value="RESET">
                            <input type="submit" class="btn btn-success btn-lg btn-block" value="LOGIN"
                                name="sub"><br><br>


                            <h6 style="color:#ffffff"><u>(Please Login to Proceed)</u></h6>

                            <div class="note">

                                <b>
                                    <center><button class="btn btn-info"
                                            style="border-radius:20px;">Instructions:</button></center>
                                    <p style="color:#ffffff"> 1.) For NSPCL Users, Default Username & password is of
                                        6-digit Employee Number.<br>
                                        2.) For NTPC Users, Default Username & password is of 6-digit Employee Number
                                        with Prefix 90
                                        (e.g <span style="color:red;">90</span>123456).<br>
                                    </p>
                                </b>

                            </div>
                            <script>
                            function resetForm() {
                                document.getElementById("form_id").reset();
                            }
                            </script>
                            <?php
                            if (isset($_REQUEST["err"])) {
                                $msg = "Invalid username or password or not permitted to access";
                                echo '<script>showErrorModal();</script>'; // Show the error modal
                            }
                            ?>
                            <script>
                            function showErrorModal() {
                                $('#errorModal').modal('show'); // Show the error modal
                            }
                            </script>
                        </form>
                        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="errorModalLabel">Error</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Invalid username or password. Please try again.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>
        <!---container contents finished-->
        <img src="images/Ntpc_logo.png" alt="Image 1">
        <img src="images/nspcl_logo1.png" alt="Image 2" style="height:90px;width:140px;"><br>
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
        <!--reset or clear fields-->
    </body>
</center>
</html>
<?php
// Database connection parameters
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

// Prepare the SQL statement
$sql = "SELECT * FROM OCMSComment order by id desc";

// Execute the SQL statement
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="images/feedback.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cantarell&display=swap');
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .feedback-container {
            width: 500px;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 5px;
        }

        .feedback-container h2 {
            text-align: center;
        }

        .feedback-container label {
            font-weight: bold;
        }

        .feedback-container input[type="text"],
        .feedback-container textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .feedback-container .emoji-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .feedback-container .emoji-container label {
            display: flex;
            align-items: center;
        }

        .feedback-container .emoji-container input[type="radio"] {
            margin-right: 5px;
        }

        .feedback-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .comments-table-container {
            width: 600px;
            border-radius: 5px;
            height: 700px; /* Define a specific height for the table container */
            overflow-y: scroll;
        }

        .comments-table {
            width: 100%;
        }

        .comments-table th,
        .comments-table td {
            padding: 10px;
            text-align: left;
        }

        .comments-table th {
            background-color: #f2f2f2;
        }
    </style>

</head>
<body>
    <div class="feedback-container">
        <h2>Feedback Form</h2><br>
        <form action="submit_feedback.php" method="POST">
            <label for="comments">Comments:</label>
            <textarea name="comments" id="comments" required></textarea>
            
            <label for="postedBy">Posted By (Your Name):</label>
            <input type="text" name="postedBy" id="postedBy" required>
            
            <label for="userDesign">User Designation:</label>
            <input type="text" name="userDesign" id="userDesign" required>
            
            <label for="userDept">User Department:</label>
            <input type="text" name="userDept" id="userDept" required>
            
            <label for="smiley">Select Smiley:</label>
            <div class="emoji-container">
                <label>
                    <input type="radio" name="smiley" value="happy" required>
                    &#128513;
                </label>
                <label>
                    <input type="radio" name="smiley" value="neutral" required>
                    &#128578;
                </label>
                <label>
                    <input type="radio" name="smiley" value="sad" required>
                    &#128545;
                </label>
            </div>
            <label for="plant">Select Plant:</label>
            <div class="plant-container">
                <label>
                    <input type="radio" name="plant" value="Bhilai" required>
                    Bhilai&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </label>
                <label>
                   <input type="radio" name="plant" value="Durgapur" required> 
                    Durgapur&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </label>
                <label>
                    <input type="radio" name="plant" value="Rourkela" required>
                    Rourkela&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </label>
                <label>
                    <input type="radio" name="plant" value="Coorporate" required>
                    Coorporate
                </label>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    <div class="table table-success table-striped comments-table-container">
        <table class="comments-table">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Comments</th>
                    <th>Reaction</th>  
                    <th>Date</th>   
                    <th>Plant</th>                 
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['dept']; ?></td>
                        <td><?php echo $row['comments']; ?></td>
                        <td><?php echo getSmileyEmoji($row['smiley']); ?></td>
                        <td><?php echo $row['C_date']->format('Y-m-d H:i:s'); ?></td>
                        <td><?php echo $row['Plant']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>

<?php
// Close the database connection
sqlsrv_close($conn);

?>

<?php
// Function to get the smiley emoji based on the value
function getSmileyEmoji($value)
{
    switch ($value) {
        case 'happy':
            return '&#128513;';
        case 'neutral':
            return '&#128578;';
        case 'sad':
            return '&#128545;';
        default:
            return '';
    }
}
?>
<?php include 'footer.php'?>

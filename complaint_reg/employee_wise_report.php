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
?>

<!DOCTYPE html>
<html>

<head>
    <title>Attended Complaint Report</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
    
    .modal {
        display: none;
    position: fixed;
    top: 0;
    left: 50%;
    bottom: 0;
    width: 50%;
    height: 100%;
    background-color: transparent;
    z-index: 9999;
    transform: translateY(100%);
    transition: transform 0.3s ease-out;
    }

    .modal.active {
        transform: translateY(0);
    }

    .modal-content {
        height: 100%;
        width: 100%;
        background-color: #D0ECE7;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 20px;
        position: relative;
    }

    .modal-content button {
       
        cursor: pointer;
        color: #E74C3C;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        background-color: transparent;
        border: none;
    }

    .custom-autocomplete-widget {
        list-style-type: none;
        padding: 0;
        max-width: 300px;
        /* Set a maximum width for the autocomplete list */
        max-height: 200px;
        /* Set a maximum height for the autocomplete list */
        overflow-y: auto;
        /* Enable vertical scrolling if the list exceeds the maximum height */
    }

    .custom-autocomplete-widget li {
        list-style: none;
        padding: 4px;
        cursor: pointer;
        background-color: aqua;
        white-space: nowrap;
        /* Prevent line breaks */
        overflow: hidden;
        /* Hide overflowed text */
        text-overflow: ellipsis;
        /* Display ellipsis (...) for overflowed text */
    }

    .custom-autocomplete-widget .ui-menu-item {
        cursor: pointer;
    }

    .custom-autocomplete-widget .ui-menu-item:hover {
        background-color: #e9e9e9;
    }

    .custom-autocomplete-widget .ui-autocomplete-messages {
        font-style: italic;
    }
    </style>
</head>

<body>

    <div class="container">
        <center>
        <h5><b><u>Employee Wise Report </u></b></h5>

                <form id="searchForm" method="POST">
                    <input type="text" id="searchInput" name="searchInput" placeholder="Search Employee Name">
                    <input type="submit" value="Generate Report">
                </form>

            <div id="searchResults"></div>

        </center>

        <div style="height: 700px; overflow: auto;">
        <table class="table table-striped" align="center" border="1.5" cellspacing="0">
            <thead style="position: sticky; top: 0; background-color: #c3e6cb;">
                <tr align="center">
                    <th>Comp.No.</th>
                    <th>Comp.Dept.</th>
                    <th>Complaint Type</th>
                    <th>Comp.Date</th>
                    <th>Comp.Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
               // Initialize the variable to store the search result
                $searchResult = "";
            // Check if the form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Check if the search input is provided
                if (!empty($_POST["searchInput"])) {
                    $searchInput = $_POST["searchInput"];

                    // Build the query to fetch the matching employee names
                    $query = "SELECT emp_num, emp_name FROM EA_webuser_tstpp WHERE emp_name LIKE '%$searchInput%'";
                    $result = sqlsrv_query($conn, $query);

                    // Check if the query was successful
                    if ($result) {
                        // Fetch the first matching row
                        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                        if ($row) {
                            // Set the search result to the employee name
                            $searchResult = $row['emp_name'];
                            //show name of the employee in the database table for later retrieval     
                            echo '<b>Employee you have search for : <u><i><SPAN style=background-color:yellow>'.$searchResult.'</span></i></u></b>';
                            // Store the input value in CT.CompUserID
                            $compUserID = $row['emp_num'];
                            //print $compUserID;

                            // Perform the modified query to fetch data
                            $query = "SELECT DISTINCT CT.CompNo AS CompNo, CT.CompDeptID AS CompDeptID, CTM.deptDesc as deptDesc,CTM.compTypeDesc AS compTypeDesc, CT.CompDate AS CompDate, CT.Status AS Status, CT.TimeStamp as TimeStamp
                                      FROM ComplaintTable CT
                                      INNER JOIN compTypeMas CTM ON CT.compTypeID = CTM.compTypeId
                                      INNER JOIN EA_webuser_tstpp WU ON CT.CompOriginDeptID = WU.dept_code
                                      WHERE CT.CompUserID = '$compUserID'
                                      ORDER BY CompDate DESC, CompNo DESC";

                            $result = sqlsrv_query($conn, $query);

                            

                            // Check if the query was successful
                            if ($result) {
                                // Fetch each row from the result set
                                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                    
                                    echo '<tr align="center">';
                                    echo '<td><a href="#" onclick="openModal(\'../complaint_reg/employee_wise_modal.php?compNo=' . $row['CompNo'] . '\')" title="Click here to see details of complaint">' . $row['CompNo'] . '</a></td>';
                                    echo '<td>' . $row['deptDesc'] . '</td>';
                                    echo '<td>' . $row['compTypeDesc'] . '</td>';
                                    // Splitting the date and time components
                                    $compDate = date_format($row['CompDate'], 'Y-m-d');
                                    $compTime = date_format($row['TimeStamp'], 'H:i:s');
                                    
                                    // Formatting and styling the date and time separately
                                    echo '<td><span style="color: blue;"> ' . $compDate . '<span style="color: red;"> [Time : ' . $compTime . ']</span></td>';
                                    echo '<td>' . $row['Status'] . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                // Handle the case when the query fails
                                $searchResult = 'Error executing the query: ' . sqlsrv_errors();
                            }
                        } else {
                            // Set the search result to indicate no match found
                            $searchResult = "No matching employee found.";
                        }
                    } else {
                        // Handle the case when the query fails
                        $searchResult = 'Error executing the query: ' . sqlsrv_errors();
                    }
                } else {
                    // Handle the case when no search input is provided
                    $searchResult = 'Please enter a search value.';
                }
            }
            ?>
            </tbody>
        </table>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script>
        $(document).ready(function() {
            $('#searchInput').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: 'search.php',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#searchInput').val(ui.item.label);
                    return false;
                }
            }).autocomplete("widget").addClass("custom-autocomplete-widget");

            $('.ui-helper-hidden-accessible').hide();
        });
        </script>
    </div>

    <!-- Modal -->
<center>
                <div id="modal" class="modal">
                    <div class="modal-content">
                        <h2></h2>
                        <iframe id="modal-iframe" frameborder="0" width="100%" height="100%"></iframe>
                        <button class="modal-close" onclick="closeModal()">
                            <img src="images/icons8-close.gif" alt="Close">&nbsp;&nbsp;CLOSE
                        </button>
                    </div>
                </div>
            </center>
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
    </center>
</body>

</html>
<?php include 'footer.php';?>
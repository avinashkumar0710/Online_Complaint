<!DOCTYPE HTML>
<html>
<head>  
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lexend&family=Playfair+Display&family=Ubuntu&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

    <style>
        /* Add your CSS styles here */
        body{font-family: 'Ubuntu', sans-serif;}
    </style>

    <script>
        window.onload = function () {
            <?php
            $serverName = "192.168.100.240";
            $connectionOptions = array(
                "Database" => "complaint",
                "UID" => "sa",
                "PWD" => "Intranet@123"
            );

            // Establish the connection
            $conn = sqlsrv_connect($serverName, $connectionOptions);

            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            $userId = $_SESSION['emp_num'];

            $plantQuery = "SELECT Plant, emp_num,dept_code,emp_name FROM [Complaint].[dbo].[EA_webuser_tstpp] WHERE emp_num = '$_SESSION[emp_num]'";
            $plantResult = sqlsrv_query($conn, $plantQuery);
            $userId = $_SESSION['emp_num'];

            if ($plantResult) {
                $row = sqlsrv_fetch_array($plantResult, SQLSRV_FETCH_ASSOC);
                $plant = $row['Plant'];
                $dept_code = $row['dept_code'];
                $emp_name = $row['emp_name'];
            }
            $query = "
                SELECT
                EA_DeptCode_Mas.DeptName,
                CompDeptID,
                COUNT(CompNo) AS CompNoCount, status
            FROM
                ComplaintTable
            JOIN
                EA_DeptCode_Mas ON ComplaintTable.CompDeptID = EA_DeptCode_Mas.Dept_id
            WHERE
                ComplaintTable.Plant = '$plant' and status in ('N')
            GROUP BY
                EA_DeptCode_Mas.DeptName, CompDeptID, status;
            ";

            $result = sqlsrv_query($conn, $query);

            if ($result === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            $dataPoints1 = array();
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $dataPoints1[] = array("label" => $row['DeptName'], "y" => intval($row['CompNoCount']));
            }

            $dataPoints1Empty = empty($dataPoints1);

            sqlsrv_free_stmt($result);
            sqlsrv_close($conn);
            ?>

            var chartContainer = document.getElementById("chartContainer");

            if (<?php echo $dataPoints1Empty ? 'true' : 'false' ?>) {
                // No data available, hide the chart and display a message
                chartContainer.style.display = "none";

                // Create and display a "No data available" message
                var noDataMessage = document.createElement("p");
                noDataMessage.innerText = "No data available";
                noDataMessage.style.textAlign = "center"; // Center-align the message
                noDataMessage.style.marginTop = "20px"; // Add some top margin
                chartContainer.parentNode.appendChild(noDataMessage);
            } else {
                // Data is available, initialize and render the chart
                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: "Department wise List of Pending Complaints" // Leave the title empty
                    },
                    axisY: {
                        includeZero: true
                    },
                    legend: {
                        cursor: "pointer",
                        verticalAlign: "center",
                        horizontalAlign: "right",
                        itemclick: toggleDataSeries
                    },
                    data: [{
                        type: "column",
                        name: "Complaint Count",
                        indexLabel: "{y}",
                        showInLegend: true,
                        dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
                    }]
                });

                chart.render();
            }
        }

        function toggleDataSeries(e) {
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else {
                e.dataSeries.visible = true;
            }
            chart.render();
        }
    </script>
</head>
<body>
    <div class="container"><br><br><br>
        <div id="chartContainer" style="height: auto; width: 100%; font-family: 'Ubuntu', sans-serif;"></div>
        <p style="text-align: center; margin-top: 20px;">Department wise List of Pending Complaints</p>
    </div>
</body>
</html>


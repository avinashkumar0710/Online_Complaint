<!DOCTYPE HTML>
<html>
<head>  
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
        
        $query = "
            SELECT
                EA_DeptCode_Mas.DeptName,
                CompDeptID,
                COUNT(CompNo) AS CompNoCount
            FROM
                ComplaintTable
            JOIN
                EA_DeptCode_Mas ON ComplaintTable.CompDeptID = EA_DeptCode_Mas.Dept_id
            WHERE
                ComplaintTable.Plant = 'NS04'
            GROUP BY
                EA_DeptCode_Mas.DeptName, CompDeptID
        ";
        
        $result = sqlsrv_query($conn, $query);
        
        if ($result === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        
        $dataPoints1 = array();
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $dataPoints1[] = array("label" => $row['DeptName'], "y" => intval($row['CompNoCount']));
        }
        
        sqlsrv_free_stmt($result);
        sqlsrv_close($conn);
    ?>

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        theme: "light2",
        title: {
            text: "Number of Complaints by Department"
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

    function toggleDataSeries(e) {
        if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
            e.dataSeries.visible = false;
        } else {
            e.dataSeries.visible = true;
        }
        chart.render();
    }
}
</script>
</head>
<body>
    <div class="container">
<div id="chartContainer" style="height: auto; width: 100%;"></div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</div>
</body>
</html>

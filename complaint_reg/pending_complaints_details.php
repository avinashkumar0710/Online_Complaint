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
//print $userId;

$plant_dptcode = "SELECT plant, dept_code FROM EA_webuser_tstpp WHERE emp_num = '$userId'";
$result1 = sqlsrv_query($conn, $plant_dptcode);

if ($result1) {
    // Fetch each row from the result set
    while ($row1 = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
        $plant = $row1['plant'];
        $dept_code = $row1['dept_code'];
        //print $plant;
        print $dept_code;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attended Complaint Report</title>

   <style>
    th {
        background-color: #f0f0f0;
        /* Set your desired header color */
    }

    .scroll-up,
    .scroll-down {
        border-radius: 9px;
        /* Set the desired border radius value */
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    table {
        height: 500px;
        height: 700px;
        overflow: auto;
    }
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
    </style>
    
</head>

<body>
    <center><h5 align="center"><u>LIST OF PENDING COMPLAINTS
<?php
if (!empty($_GET["compType"])) {
    echo ' "' . ((int)$_GET["compType"]) . '"';
}
?>
 WITH DESCRIPTION AS ON Date : "<?php echo (date('Y-m-d')); ?>"</u><br></h5>
    <div class="container"><br>

<label><i><u>Please Select Complaint Type:</u></i></label>&nbsp;<br>
<form method="POST" >
    <select name="department" aria-placeholder="Please Select Complaint Type">
        <option value="">Select Complaint Type</option> <!-- Add this line for the default option -->
        <?php
        // Assuming you have a database connection established using sqlsrv_connect

        $query = "select Distinct compTypeDesc from compTypeMas where deptID='$dept_code' and Plant='$plant'";
        $result = sqlsrv_query($conn, $query);

        // Check if the query was successful
        if ($result) {
            // Fetch each row from the result set
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $deptDesc = $row['compTypeDesc'];
                // Check if the current option matches the selected department
                if (isset($_POST["department"]) && $_POST["department"] == $deptDesc) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo '<option value="' . $deptDesc . '" ' . $selected . '>' . $deptDesc . '</option>';
            }
        } else {
            // Handle the case when the query fails
            echo 'Error executing the query: ' . sqlsrv_errors();
        }

        // Close the database connection
        //sqlsrv_close($conn);
        ?>
    </select>
    <input type="submit" value="Generate Report">
    </form>

    

 <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedType = $_POST['department'];

    // Check if a complaint type is selected
    if (!empty($selectedType)) {
        $query = "SELECT DISTINCT CT.CompNo AS CompNo, CT.Time AS CompTime, CT.CompDate AS CompDate, EMS.loc_desc AS Plant, CT.Location AS Location,
            CT.compTypeID AS compTypeID, CT.Description AS Description, CTM.compTypeDesc AS compTypeDesc, CAD.Remarks AS Remarks,
            CT.CompDeptID AS CompDeptID, CT.Status AS Status
            FROM ComplaintTable CT 
            JOIN compTypeMas CTM ON CT.compTypeID = CTM.compTypeId
            JOIN ComplaintAttendDet CAD ON CT.CompNo = CAD.CompNo 
            JOIN emp_mas_sap EMS ON CT.Plant = EMS.location 
            WHERE CT.CompDeptID = '$dept_code'
            AND CT.Status != 'C'
            AND CTM.compTypeDesc = '$selectedType'
            ORDER BY CT.CompNo desc";
    } else {
        $query = "SELECT DISTINCT CT.CompNo AS CompNo, CT.Time AS CompTime, CT.CompDate AS CompDate, EMS.loc_desc AS Plant, CT.Location AS Location,
            CT.compTypeID AS compTypeID, CT.Description AS Description, CTM.compTypeDesc AS compTypeDesc, CAD.Remarks AS Remarks,
            CT.CompDeptID AS CompDeptID, CT.Status AS Status
            FROM ComplaintTable CT 
            JOIN compTypeMas CTM ON CT.compTypeID = CTM.compTypeId
            JOIN ComplaintAttendDet CAD ON CT.CompNo = CAD.CompNo 
            JOIN emp_mas_sap EMS ON CT.Plant = EMS.location 
            WHERE CT.CompDeptID = '$dept_code'
            AND CT.Status != 'C'
            ORDER BY CT.CompNo desc";
    }
} else {
    $query = "SELECT DISTINCT CT.CompNo AS CompNo, CT.Time AS CompTime, CT.CompDate AS CompDate, EMS.loc_desc AS Plant, CT.Location AS Location,
        CT.compTypeID AS compTypeID, CT.Description AS Description, CTM.compTypeDesc AS compTypeDesc, CAD.Remarks AS Remarks,
        CT.CompDeptID AS CompDeptID, CT.Status AS Status
        FROM ComplaintTable CT 
        JOIN compTypeMas CTM ON CT.compTypeID = CTM.compTypeId
        JOIN ComplaintAttendDet CAD ON CT.CompNo = CAD.CompNo 
        JOIN emp_mas_sap EMS ON CT.Plant = EMS.location 
        WHERE CT.CompDeptID = '$dept_code'
        AND CT.Status != 'C'
        ORDER BY CT.CompNo desc";
}

    $result = sqlsrv_query($conn, $query);

    if ($result) {
        // Check if any records are found
        if (sqlsrv_has_rows($result)) {
            echo '<div style="height: 650px; overflow: auto;">';
            echo '<table class="table table-striped" align="center" border="1.5" cellspacing="0">
            <thead style="position: sticky; top: 0; background-color: #c3e6cb;">
                <tr align="center">
                    <th valign="top">S.No.</th>
                    <th valign="top">Comp.No.</th>
                    <th valign="top">Plant</th>
                    <th valign="top">Complaint Date</th>
                    <th valign="top">Location</th>
                    <th valign="top">Comp. Type</th>
                    <th valign="top">Comp. Status</th>
                    <th valign="top">Complaint Description</th>
                    </tr>
                    </thead>';
    
            $counter = 1;
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo '<tr align="center">
                    <td>' . $counter . '</td>
                    <td>' . $row['CompNo'] . '</td>
                    <td>' . $row['Plant'] . '</td>
                    <td><font color="red">' . date_format($row['CompDate'], 'Y-m-d') . '</font><font color="blue">[' . $row['CompTime'] . ']</font></td>
                    <td>' . strtoupper($row['Location']) . '</td>
                    <td>' . $row['compTypeDesc'] . '</td>
                    <td>' . $row['Status'] . '</td>
                    <td>' . strtoupper($row['Description']) . '</td>
                </tr>';
    
                $counter++;
            }
    
            echo '</table>';
        } else {
            echo '<p align="center">NO RECORDS EXIST</p>';
        }
    } else {
        // Handle the case when the query fails
        echo 'Error executing the query: ' . sqlsrv_errors();
    }

?>
<?php include 'footer.php';?>



<?php
// Assuming you have a database connection established using sqlsrv_connect

if (isset($_POST['department'])) {
    $selectedType = $_POST['department'];

    // Build the query to retrieve complaint data based on the selected complaint type
    $query = "SELECT * FROM ComplaintTable WHERE compTypeID = (SELECT compTypeId FROM compTypeMas WHERE compTypeDesc = '$selectedType')";

    // Execute the query
    $result = sqlsrv_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        if (sqlsrv_has_rows($result)) {
            echo '<table>';
            echo '<tr>';
            echo '<th>Complaint Number</th>';
            echo '<th>Plant</th>';
            echo '<th>Complaint Date</th>';
            // Add more table headers as needed

            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $row['CompNo'] . '</td>';
                echo '<td>' . $row['Plant'] . '</td>';
                echo '<td>' . $row['CompDate']->format('Y-m-d') . '</td>';
                // Add more table cells as needed
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No records found.</p>';
        }
    } else {
        echo '<p>Error executing the query: ' . sqlsrv_errors() . '</p>';
    }
} else {
    echo '<p>Please select a complaint type.</p>';
}

// Close the database connection
// sqlsrv_close($conn);
?>

<?php
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
//$compDeptID = $_COOKIE["userDept"];
//$deptDesc = $row['deptDesc'];
$strSelComp = "SELECT CA.AttendedDate as AttendDate, CA.[Time] as AttendTime, CT.CompNo as CompNo, CT.Time as CompTime, CT.CompDate as CompDate, CA.AttendedByUserID as AttendedByUserID, CT.compTypeID as compTypeID, CT.Status as Status, " .
        " CTM.compTypeDesc as compTypeDesc, CTM.deptDesc as DeptDesc, EWT.emp_name as emp_name, CT.CompOriginDeptID AS compOriginDeptID " .
        " FROM ComplaintTable CT, compTypeMas CTM, ComplaintAttendDet CA, EA_webuser_tstpp EWT" .
        " WHERE CT.compTypeID = CTM.compTypeId AND " .
        " CT.CompDeptID = '" . $compDeptID . "' AND " .
        " CT.CompNo = CA.CompNo AND " .
        " CT.Status = 'C' " .
        " and EWT.emp_num = CA.AttendedByUserID " .
        " AND CT.CompDate >= '" . $startDate . "' AND CT.CompDate <= '" . $endDate . "'" .
        " ORDER BY CompDate DESC, compNo DESC";

$objRS = sqlsrv_query($conn, $strSelComp);

if ($objRS === false || sqlsrv_fetch($objRS) === null) {
    echo "NO RECORDS";
} else {
    //echo "<h3 align=center><u>Complaints Attended By Your Department " . showDeptName($compDeptID) . "</u></h3>";
    echo '<table align=center width=90% border=1 cellspacing=0>';
    echo '<tr>
            <th valign=top>Comp.No.
            <th align=center>Comp.Dept.
            <th align=center>Complaint Attend By
            <th valign=top>Complaint Type
            <th valign=top>Comp.Date
            <th valign=top>Attend.Date
            <th valign=top>Comp.Status</th>';

    while ($row = sqlsrv_fetch_array($objRS, SQLSRV_FETCH_ASSOC)) {
        echo '<tr>
                <td>' . $row["CompNo"] . '</td>
                <td>' . $row["compOriginDeptID"] . '</td>
                <td>' . $row["emp_name"] . '</td>
                <td><a title="Click here to see details of complaint" onMouseOut="javascript:window.status=\'\'" onMouseMove="javascript:window.status=\'Click here to see details of complaint\'" onMouseOver="javascript:window.status=\'Click here to see details of complaint\'" href="/deptCompRegister/attendComp.asp?compNo=' . $row["CompNo"] . '">' . $row["compTypeDesc"] . '</a></td>
                <td>' . $row["CompDate"] . ' <font color="blue">Time:<font color="red">[' . $row["CompTime"] . ']<font color="blue"></td>
                <td>' . $row["AttendDate"] . ' <font color="blue">Time:<font color="red">[' . $row["AttendTime"] . ']<font color="blue"></td>
                <td>' . $row["Status"] . '</td>
            </tr>';
    }

    echo '</table>';
}
?>

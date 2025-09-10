<?php
global $db;
require_once '../db/database.php';

$id = $_GET['id'];
$query = 'SELECT * FROM `year_data` WHERE id = $id';

$result = mysqli_query($db, $query)
or die('Error ' . mysqli_error($db) . ' with query ' . $query);

$years = mysqli_fetch_assoc($result);


mysqli_close($db);
?>
<html>

</html>

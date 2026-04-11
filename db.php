
<?php
$conn = new mysqli("localhost","root","password","srms");
if($conn->connect_error){
    die("DB Connection Failed");
}
?>

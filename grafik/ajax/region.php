<option value="">...</option>
<?php
if(isset($_GET['state_id'])){
    $state_id = $_GET['state_id'];
    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
    if($conn->connect_error) {
        die('Ошибка соединения: ' . $conn->connect_error);
    }
    $result = $conn->query("select id, name from region where state_id = $state_id order by name");
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<option value='".$row['id']."'>".$row["name"]."</option>";
        }
    }
    $conn->close();
}
?>
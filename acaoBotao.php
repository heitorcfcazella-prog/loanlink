<?php 
include("db.php");

if (isset($_POST['postar'])) {
  $id_item = $_POST['id_item'];

  $query = "INSERT INTO postagens (id_item)
            VALUES (?);";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();
  $stmt->close();

  header("Location: dashboard.php");
}


?>
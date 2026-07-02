<?php
include("db.php");

if (isset($_POST['postar'])) {
  $status = $_POST['status'];
  if ($status == "emprestado") {
    header("Location: dashboard.php");
  } else {
    $id_item = $_POST['id_item'];

    $query = "INSERT INTO postagens (id_item)
              VALUES (?);";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_item);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
  }
}
if (isset($_POST['deletar'])) {
  $id_item = $_POST['id_item'];

  $query = "DELETE FROM itens
            WHERE id_item = ?";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();
  $stmt->close();

  header("Location: dashboard.php");
}
if (isset($_POST['editar'])) {
  $id_item = $_POST['id_item'];
  header("Location: editarItem.php?id_item=" . $id_item);
}

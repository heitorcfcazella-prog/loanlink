<?php 
include("db.php");

if (isset($_POST['emprestimo'])) {
  $id_item = $_POST['id_item'];
  $id_usuario = $_POST['id_usuario'];

  //query para saber o proprietario
  $query_proprietario = "SELECT id_proprietario FROM itens WHERE id_item = ?;";

  $stmt = $conn->prepare($query_proprietario);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();

  $resultado = $stmt->get_result();
  $dados = $resultado->fetch_assoc();
  $id_proprietario = $dados['id_proprietario'];
  
  $query_solicitacao = "INSERT INTO emprestimos (id_item, id_proprietario, id_emprestado)
                        VALUES(?, ?, ?);";

  $stmt = $conn->prepare($query_solicitacao);
  $stmt->bind_param("iii", $id_item, $id_proprietario, $id_usuario);
  $stmt->execute();
  $stmt->close();

  header("Location: dashboard.php");
}



?>
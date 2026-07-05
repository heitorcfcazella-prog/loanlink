<?php 
include("./includes/db.php");

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

  $query_atualiza_item = "UPDATE itens
                          SET status = 'pendente'
                          WHERE id_item = ?";

  $stmt = $conn->prepare($query_atualiza_item);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();

  header("Location: dashboard.php");
  exit;
}

if (isset($_POST['aprovar'])) {
  $id_emprestimo = $_POST['id_emprestimo'];
  $id_item = $_POST['id_item'];
  // data aprovacao, data prevista (7dias), status = 'emprestado'
  $query_aprovado = "UPDATE emprestimos 
                     SET data_aprovacao = NOW(), data_prevista_devolucao = DATE_ADD(CURDATE(), INTERVAL 7 DAY), status = 'emprestado'
                     WHERE id_emprestimo = ?";
  
  $stmt = $conn->prepare($query_aprovado);
  $stmt->bind_param("i", $id_emprestimo);
  $stmt->execute();
  
  $query_atualiza_item_emitens = "UPDATE itens
                                  SET status = 'emprestado'
                                  WHERE id_item = ?";

  $stmt = $conn->prepare($query_atualiza_item_emitens);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();

  $query_atualiza_item_empostagens = "UPDATE postagens
                                      SET ativo = 0
                                      WHERE id_item = ?";

  $stmt = $conn->prepare($query_atualiza_item_empostagens);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();

  $query_cancela_pedidos = "UPDATE emprestimos
                            SET status = 'cancelado'
                            WHERE id_item = ?
                            AND status = 'pendente'
                            AND id_emprestimo <> ?;";

  $stmt = $conn->prepare($query_cancela_pedidos);
  $stmt->bind_param("ii", $id_item, $id_emprestimo);
  $stmt->execute();

  header("Location: dashboard.php");
  exit;
}

if (isset($_POST['recusar'])) {
  $id_item = $_POST['id_item'];
  //UPDATE item = 'disponivel'/emprestimos = 'cancelado'

  $query_atualiza_item_emitens = "UPDATE itens
                                  SET status = 'disponivel'
                                  WHERE id_item = ?";

  $stmt = $conn->prepare($query_atualiza_item_emitens);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();

  $query_atualiza_emprestimo = "UPDATE emprestimos
                                SET status = 'cancelado'
                                WHERE id_item = ?";

  $stmt = $conn->prepare($query_atualiza_emprestimo);
  $stmt->bind_param("i", $id_item);
  $stmt->execute();

  header("Location: dashboard.php");
  exit;
}
if (isset($_POST['devolver'])) {
  /* emprestimo - id_emprestimo
    data_devolucao = CURDATE()
    status = devolvido
    item - id_item
    status = disponivel
  */
    $id_emprestimo = $_POST['id_emprestimo'];
    $id_item = $_POST['id_item'];

    $query_emprestimo = "UPDATE emprestimos
                         SET data_devolucao = CURDATE(), status = 'devolvido'
                         WHERE id_emprestimo = ?";
    $stmt = $conn->prepare($query_emprestimo);
    $stmt->bind_param("i", $id_emprestimo);
    $stmt->execute();

    $query_item = "UPDATE itens
                   SET status = 'disponivel'
                   WHERE id_item = ?";
    $stmt = $conn->prepare($query_item);
    $stmt->bind_param("i", $id_item);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}


?>
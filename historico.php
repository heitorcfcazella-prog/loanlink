<?php
include("db.php");
include("header.php");

if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit;
} else {
  $nome = $_SESSION['nome'];
  $id_usuario = $_SESSION['id_usuario'];

  $status = $_GET['status'] ?? 'todos';
  
  if ($status === '') {
    $status = 'todos';
  }

  $mapaStatus = [
    'pendentes'   => 'pendente',
    'emprestados' => 'emprestado',
    'devolvidos'  => 'devolvido',
    'cancelados'  => 'cancelado'
  ];

  $filtroStatus = '';
  if ($status !== 'todos' && isset($mapaStatus[$status])) {
    $filtroStatus = " AND e.status = '" . $mapaStatus[$status] . "'";
  }

  $query = "
  SELECT
    e.id_emprestimo,
    e.data_solicitacao,
    e.data_aprovacao,
    e.data_prevista_devolucao,
    e.data_devolucao,
    i.id_item,
    i.nome AS nome_item,
    i.categoria,
    i.foto_item,
    up.nome AS nome_proprietario,
    ue.nome AS nome_emprestado
  FROM emprestimos e
  INNER JOIN itens i ON e.id_item = i.id_item
  INNER JOIN usuarios up ON e.id_proprietario = up.id_usuario
  INNER JOIN usuarios ue ON e.id_emprestado = ue.id_usuario
  WHERE e.id_proprietario = ? $filtroStatus
  ORDER BY e.data_solicitacao DESC
";

  $itens = [];

  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();
  while ($item = $resultado->fetch_assoc()) {
    $itens[] = $item;
  }
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico</title>
</head>

<body>
  <p>Quais você deseja buscar? </p>
  <form method="GET" action="historico.php">
    <select name="status" onchange="this.form.submit()">
      <option value="">--------------------</option>
      <option value="todos">Todos</option>
      <option value="pendentes">Pendentes</option>
      <option value="emprestados">Emprestados</option>
      <option value="devolvidos">Devolvidos</option>
      <option value="cancelados">Cancelados</option>
    </select>
  </form>
  <hr>
  <h2>Itens</h2>
  <?php foreach ($itens as $item) { ?>
    <div>
      <h3><?= htmlspecialchars($item['nome_item']) ?></h3>
      <img src="<?= htmlspecialchars($item['foto_item']) ?>" alt="Foto Não Disponível" style="height: 200px;">
      <p>Dono: <?= htmlspecialchars($item['nome_proprietario']) ?></p>
      <p>Solicitante: <?= htmlspecialchars($item['nome_emprestado']) ?></p>
      <p>Data Solicitação: <?= htmlspecialchars($item['data_solicitacao']) ?></p>
      <p>Data Aprovação: <?= $item['data_aprovacao'] !== null
                            ? htmlspecialchars($item['data_aprovacao'])
                            : 'Ainda não aprovado' ?></p>
      <p>Data Prevista de Devolução: <?= $item['data_prevista_devolucao'] !== null
                                        ? htmlspecialchars($item['data_prevista_devolucao'])
                                        : 'Ainda não calculado' ?></p>
      <p>Data de Devolução: <?= $item['data_devolucao'] !== null
                              ? htmlspecialchars($item['data_devolucao'])
                              : 'Ainda não devolvido' ?></p>
      <hr>
    </div>
  <?php } ?>
</body>

</html>
<?php
include("footer.html");
?>
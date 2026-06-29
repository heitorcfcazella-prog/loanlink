<?php
include("header.php");
include("db.php");

if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit;
} else {
  $nome = $_SESSION['nome'];
  $id_usuario = $_SESSION['id_usuario'];

  //pegar informações de emprestimos e cadastros do banco

  //query para as informações (atualiza quando precisar de mais infos)

  $query = "SELECT
            (SELECT COUNT(*)
            FROM itens
            WHERE id_proprietario = ?) AS total_itens,

            (SELECT COUNT(*)
            FROM emprestimos
            WHERE id_proprietario = ?
            AND status = 'emprestado') AS emprestados_por_mim,

            (SELECT COUNT(*)
            FROM emprestimos
            WHERE id_emprestado = ?
            AND status = 'emprestado') AS emprestados_para_mim,

            (SELECT COUNT(*)
            FROM emprestimos
            WHERE id_proprietario = ?
            AND status = 'pendente') AS pendentes
        ";
  //AS total_itens atribui um nome a esse resultado(tipo uma variavel)
  $stmt = $conn->prepare($query);
  $stmt->bind_param("iiii", $id_usuario, $id_usuario, $id_usuario, $id_usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();
  $dados = $resultado->fetch_assoc();

  //variáveis com as informações necessárias

  $total_itens = $dados['total_itens'];
  $emprestados_por_mim = $dados['emprestados_por_mim'];
  $emprestados_para_mim = $dados['emprestados_para_mim'];
  $pendentes = $dados['pendentes'];


  //retorna os itens
  $query_itens = "SELECT id_item, nome, categoria, descricao, status, foto_item
                  FROM itens
                  WHERE id_proprietario = ?";

  $stmt_itens = $conn->prepare($query_itens);
  $stmt_itens->bind_param("i", $id_usuario);
  $stmt_itens->execute();

  $resultado_itens = $stmt_itens->get_result();

  $meus_itens = [];

  while ($item = $resultado_itens->fetch_assoc()) {
    $meus_itens[] = $item;
  }

  //query emprestados por mim
  $query_emprestados = "SELECT
                        i.nome AS item,
                        u.nome AS emprestado_para,
                        e.data_prevista_devolucao
                        FROM emprestimos e
                        JOIN itens i ON e.id_item = i.id_item
                        JOIN usuarios u ON e.id_emprestado = u.id_usuario
                        WHERE e.id_proprietario = ?
                        AND e.status = 'emprestado'
                        ORDER BY e.data_solicitacao DESC";

  $stmt = $conn->prepare($query_emprestados);
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();

  $emprestados_por_mim_itens = [];

  while ($emprestimo = $resultado->fetch_assoc()) {
    $emprestados_por_mim_itens[] = $emprestimo;
  }

  //query emprestados para mim
  $query_emprestados = "SELECT
                        i.nome AS item,
                        u.nome AS emprestado_por,
                        e.data_prevista_devolucao
                        FROM emprestimos e
                        JOIN itens i ON e.id_item = i.id_item
                        JOIN usuarios u ON e.id_proprietario = u.id_usuario
                        WHERE e.id_emprestado = ?
                        AND e.status = 'emprestado'
                        ORDER BY e.data_solicitacao DESC";

  $stmt = $conn->prepare($query_emprestados);
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();

  $emprestados_para_mim_itens = [];

  while ($emprestado = $resultado->fetch_assoc()) {
    $emprestados_para_mim_itens[] = $emprestado;
  }

  //query itens pendentes
  $query_pendentes = "SELECT
                      e.id_emprestimo,
                      i.nome AS item,
                      u.nome AS solicitante,
                      e.data_solicitacao,
                      e.data_prevista_devolucao,
                      e.status
                      FROM emprestimos e
                      JOIN itens i ON e.id_item = i.id_item
                      JOIN usuarios u ON e.id_emprestado = u.id_usuario
                      WHERE e.id_proprietario = ?
                      AND e.status = 'pendente'
                      ORDER BY e.data_solicitacao DESC";

  $stmt = $conn->prepare($query_pendentes);
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();

  $pendentes_itens = [];

  while ($pendente = $resultado->fetch_assoc()) {
    $pendentes_itens[] = $pendente;
  }

  if (isset($_POST["logout"])) {
    session_destroy(); // deslogar
    header("Location: login.php");
  }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .titulo-detalhes {
      font-size: 1.5rem;
      /* tamanho parecido com h2 */
      font-weight: bold;
      cursor: pointer;
    }

    img {
      height: 200px;
    }
  </style>
  <title>DashBoard</title>
</head>

<body>
  <h1>Olá <?= $nome ?></h1>
  <p>Seu id é <?= $id_usuario ?></p>
  <a href="#meus_itens">
    <p>Itens cadastrados: <?= $total_itens ?> </p>
  </a>
  <a href="#emprestados_por_mim">
    <p>Emprestados por você: <?= $emprestados_por_mim ?></p>
  </a>
  <a href="#emprestados_para_mim">
    <p>Emprestados para você: <?= $emprestados_para_mim ?></p>
  </a>
  <a href="#pendentes">
    <p>Solicitações pendentes: <?= $pendentes ?></p>
  </a>

  <details id="meus_itens">
    <summary class="titulo-detalhes">Meus Itens</summary>
    <a href="adicionarItens.php">Adicionar Itens</a>
    <?php foreach ($meus_itens as $item) { ?>
      <div>
        <h3><?= htmlspecialchars($item['nome']) ?></h3>

        <img src="<?= htmlspecialchars($item['foto_item']) ?>" alt="Foto Não Disponível">

        <p>Categoria: <?= htmlspecialchars($item['categoria']) ?></p>

        <p>Descrição: <?= htmlspecialchars($item['descricao']) ?></p>

        <p>Status: <?= htmlspecialchars($item['status']) ?></p>

        <form action="acaoBotao.php" method="post">
          <input type="hidden" name="id_item" value="<?= $item['id_item'] ?>"> <!--valor invisivel para o 'id_item' ser acessado-->
          <button type="submit" name="postar">Postar Item</button>
          <button type="submit" name="editar">Editar Item</button>
          <button type="submit" name="deletar">Deletar Item</button>
        </form>
        <hr>
      </div>
    <?php } ?>
  </details>


  <details id="emprestados_por_mim">
    <summary class="titulo-detalhes">Emprestados por mim: </summary>
    <?php foreach ($emprestados_por_mim_itens as $emprestimo) { ?>
      <div>
        <h3><?= htmlspecialchars($emprestimo['item']) ?></h3>

        <p>Emprestado para: <?= htmlspecialchars($emprestimo['emprestado_para']) ?></p>

        <p>Data de devolução: <?= htmlspecialchars($emprestimo['data_prevista_devolucao']) ?></p>
        <hr>
      </div>
    <?php }; ?>
  </details>

  <details id="emprestados_para_mim">
    <summary class="titulo-detalhes">Emprestados para mim</summary>
    <?php foreach ($emprestados_para_mim_itens as $emprestado) { ?>
      <div>
        <h3><?= htmlspecialchars($emprestado['item']) ?></h3>

        <p>Emprestado por: <?= htmlspecialchars($emprestado['emprestado_por']) ?></p>

        <p>Data de devolução: <?= htmlspecialchars($emprestado['data_prevista_devolucao']) ?></p>
        <hr>
      </div>
    <?php } ?>
  </details>

  <details id="pendentes">
    <summary class="titulo-detalhes">Pendentes</summary>
    <?php foreach ($pendentes_itens as $pendente) { ?>
      <div>
        <h3><?= htmlspecialchars($pendente['item']) ?></h3>

        <p>Solicitado por: <?= htmlspecialchars($pendente['solicitante']) ?></p>

        <p>Data de solicitação: <?= htmlspecialchars($pendente['data_solicitacao']) ?></p>

        <form action="solicitacao.php" method="post">
          <input type="hidden" name="id_emprestimo" value="<?= $pendente['id_emprestimo'] ?>">

          <button type="submit" name="acao" value="aprovar">Aprovar</button>

          <button type="submit" name="acao" value="recusar">Recusar</button>
        </form>
        <hr>
      </div>
    <?php } ?>
  </details>


  <form action="dashboard.php" method="post">
    <input type="submit" value="Deslogar" name="logout">
  </form>
</body>

</html>
<?php
include("footer.html");
?>
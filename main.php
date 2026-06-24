<?php
include("db.php");
session_start();

if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit;
} else {
  include("header.php");
  $nome = $_SESSION['nome'];
  $id_usuario = $_SESSION['id_usuario'];

  //manter em itens para teste
  //mudar para tabela postagem depois de mudar o dashboard.php para ter esse botão e lógica
$query = "SELECT
            u.id_usuario AS id_dono,
            u.nome AS nome_dono,
            i.id_proprietario,
            i.id_item,
            i.nome AS nome_item,
            i.categoria,
            i.descricao,
            i.foto_item
          FROM itens i
          JOIN usuarios u ON u.id_usuario = i.id_proprietario
          WHERE i.id_proprietario != ?
          ORDER BY i.id_item DESC
          LIMIT 10";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();

  $postagens = [];

  while ($postagem = $resultado->fetch_assoc()) {
    $postagens[] = $postagem;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área de Trocas</title>
</head>

<body>
  <?php foreach ($postagens as $postagem) { ?>
    <div>
      <h2><?= htmlspecialchars($postagem['nome_item']) ?></h2>
      <p><b>Dono do item: </b><?= htmlspecialchars($postagem['nome_dono']) ?></p>
      <img src="<?= htmlspecialchars($postagem['foto_item']) ?>" alt="Foto Não Disponível" style="height: 200px;">
      <p><b>Categoria:</b> <?= htmlspecialchars($postagem['categoria']) ?></p>
      <p><b>Descrição:</b> <?= htmlspecialchars($postagem['descricao']) ?></p>
      <p>ID: <?= $postagem['id_item'] ?></p>
      <form action="" method="post">
        <button type="submit"
          name="id_item"
          value="<?= $postagem['id_item'] ?>">
          Solicitar Empréstimo
        </button>
      </form>
      <hr>
    </div>
  <?php } ?>
</body>

</html>
<?php
include("footer.html");
?>
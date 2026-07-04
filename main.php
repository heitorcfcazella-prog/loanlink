<?php
include("db.php");
include("header.php");

if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit;
} else {
  $nome = $_SESSION['nome'];
  $id_usuario = $_SESSION['id_usuario'];

  $query = "SELECT
            u.id_usuario AS id_dono,
            u.nome AS nome_dono,
            i.id_proprietario,
            i.id_item,
            i.nome AS nome_item,
            i.categoria,
            i.descricao,
            i.foto_item,
            p.id_postagem,
            p.data_postagem
          FROM postagens p
          JOIN itens i ON p.id_item = i.id_item
          JOIN usuarios u ON u.id_usuario = i.id_proprietario
          WHERE i.id_proprietario != ?
          AND p.ativo = 1
          ORDER BY p.data_postagem DESC
          LIMIT 10";

  //p é do postagens, só puxa o que está ativo e pega pelos mais novos primeiro
  //adicionar algoritmo depois (sistema de likes)

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
      <p><b>Dono do item: </b><a href="perfil.php?id_usuario=<?= htmlspecialchars($postagem['id_dono']) ?>"><?= htmlspecialchars($postagem['nome_dono']) ?></a></p>
      <img src="<?= htmlspecialchars($postagem['foto_item']) ?>" alt="Foto Não Disponível" style="height: 200px;">
      <p><b>Categoria:</b> <?= htmlspecialchars($postagem['categoria']) ?></p>
      <p><b>Descrição:</b> <?= htmlspecialchars($postagem['descricao']) ?></p>
      <p>ID: <?= $postagem['id_item'] ?></p>
      <p>ID Usuário: <?= $id_usuario ?></p>
      <form action="solicitacao.php" method="post">
        <input type="hidden" name="id_item" value="<?= $postagem['id_item'] ?>">
        <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">
        <button type="submit"
          name="emprestimo">
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
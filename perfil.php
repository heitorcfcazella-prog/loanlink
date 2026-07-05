<?php
include("./includes/db.php");
include("./includes/header.php");

if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit;
} else {
  $nome = $_SESSION['nome'];
  $id_usuario = $_SESSION['id_usuario'];
  $id_perfil = filter_input(INPUT_GET, 'id_usuario', FILTER_VALIDATE_INT);

  if (($id_perfil === null || $id_perfil === false) || ($id_perfil === $id_usuario)) {
    header("Location: dashboard.php");
    exit;
  } else {
    //query_perfil = foto_perfil, nome, email
    $query_perfil = "SELECT foto_perfil, nome, email FROM usuarios
                     WHERE id_usuario = ?";

    $stmt = $conn->prepare($query_perfil);
    $stmt->bind_param("i", $id_perfil);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $perfil = $resultado->fetch_assoc();

    //se perfil não existir
    if (!$perfil) {
      header("Location: dashboard.php");
      exit;
    } else {

      //query_quantidades = itens e emprestimos
      $query_quantidades = "SELECT 
                          (SELECT COUNT(*)
                          FROM emprestimos
                          WHERE id_proprietario = ?) AS quantidade_emprestimos,
                          
                          (SELECT COUNT(*)
                          FROM itens
                          WHERE id_proprietario = ?) AS quantidade_itens";

      $stmt = $conn->prepare($query_quantidades);
      $stmt->bind_param("ii", $id_perfil, $id_perfil);
      $stmt->execute();

      $resultado = $stmt->get_result();
      $dados = $resultado->fetch_assoc();

      $quantidade_emprestimos = $dados['quantidade_emprestimos'];
      $quantidade_itens = $dados['quantidade_itens'];

      $query_itens = "SELECT 
                      i.id_item AS itens_id, 
                      i.nome, 
                      i.categoria, 
                      i.descricao, 
                      i.foto_item,
                      i.id_proprietario,
                      p.id_postagem,
                      p.id_item
                      FROM itens i
                      JOIN postagens p ON i.id_item = p.id_item
                      WHERE id_proprietario = ? 
                      AND p.ativo = 1";

      $stmt = $conn->prepare($query_itens);
      $stmt->bind_param("i", $id_perfil);
      $stmt->execute();

      $resultado = $stmt->get_result();

      $itens = [];

      while ($item = $resultado->fetch_assoc()) {
        $itens[] = $item;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .perfil, .item {
      display: flex;
      align-items: center;
      gap: 4rem;
    }

    .perfil img {
      width: 180px;
      height: 180px;
      border-radius: 50%;
    }
    .item img {
      width: 180px;
      height: 180px;
    }

    .informacoes_perfil, .informacoes_item {
      display: flex;
      flex-direction: column;
    }
  </style>
  <title>Perfil</title>
</head>

<body>
  <div class="perfil">
    <img src="<?= $perfil['foto_perfil'] ?>" alt="Foto Não encontrada">
    <div class="informacoes_perfil">
      <h2><?= $perfil['nome'] ?></h2>
      <p><b>Email: </b><?= $perfil['email'] ?></p>
      <br>
      <p><b>Quantidade de Itens Cadastrados: </b><?= $quantidade_itens ?></p>
      <p><b>Quantidade de Empréstimos Realizados: </b><?= $quantidade_emprestimos ?></p>
    </div>
  </div>
  <hr>
  <h2>Itens: </h2>
  <?php foreach ($itens as $item) { ?>
    <div class="item">
      <img src="<?= htmlspecialchars($item['foto_item']) ?>" alt="Foto Não Disponível" style="height: 200px;">
      <div class="informacoes_item">
        <h2><?= htmlspecialchars($item['nome']) ?></h2>
        <p><b>Categoria: </b><?= htmlspecialchars($item['categoria']) ?></p>
        <p><?= htmlspecialchars($item['descricao']) ?></p>
        <form action="solicitacao.php" method="post">
          <input type="hidden" name="id_item" value="<?= $item['itens_id'] ?>">
          <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">
          <button type="submit"
            name="emprestimo">
            Solicitar Empréstimo
          </button>
        </form>
      </div>
    </div>
    <hr>

  <?php } ?>
</body>

</html>
<?php
include("./includes/footer.html");
?>
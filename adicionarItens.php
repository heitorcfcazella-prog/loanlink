<?php
include("db.php");
session_start();

if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit;
} else {
  $id_usuario = $_SESSION['id_usuario'];

  //query para as categorias
  $categorias_stmt = $conn->prepare("
                      SELECT COLUMN_TYPE
                      FROM INFORMATION_SCHEMA.COLUMNS
                      WHERE TABLE_SCHEMA = DATABASE ()
                        AND TABLE_NAME = ?
                        AND COLUMN_NAME = ?
  ");

  $tabela = "itens";
  $coluna = "categoria";

  $categorias_stmt->bind_param("ss", $tabela, $coluna);
  $categorias_stmt->execute();
  $result = $categorias_stmt->get_result();
  $row = $result->fetch_assoc();

  preg_match("/^enum\((.*)\)$/", $row['COLUMN_TYPE'], $matches);

  $categorias = str_getcsv($matches[1], ',', "'");
  $categorias_stmt->close();
}
include("header.html");
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adicionar Itens</title>
</head>

<body>
  <h1>Adicionar Itens</h1>
  <form action="adicionarItens.php" method="post">
    <label>Nome: </label>
    <input type="text" name="nome" id="nome" required><br>
    <label>Descrição: </label>
    <input type="text" name="descricao" id="descricao" required><br>
    <select name="categoria">
      <?php foreach ($categorias as $categoria): ?>
        <option value="<?= htmlspecialchars($categoria) ?>">
          <?= htmlspecialchars($categoria) ?>
        </option>
      <?php endforeach; ?>
    </select><br>
    <label>Escolha uma foto:</label><br>
    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp"><br>

    <input type="submit" name="enviar" value="Cadastrar Item"><br>
    <?php if (!empty($erro)): ?>
      <p class="erro"><?= $erro ?></p>
    <?php endif; ?>
  </form>
</body>

</html>
<?php
include("footer.html");
?>
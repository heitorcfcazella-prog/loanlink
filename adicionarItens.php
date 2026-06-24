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

  if (isset($_POST['enviar'])) {
    $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
    $descricao = filter_input(INPUT_POST, "descricao", FILTER_SANITIZE_SPECIAL_CHARS);
    $categoriaSelecionada = filter_input(INPUT_POST, "categoria", FILTER_SANITIZE_SPECIAL_CHARS);
    if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
      $erro .= "<p class='alerta'>Selecione uma foto.</p>";
    } else {
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mime = $finfo->file($_FILES['foto']['tmp_name']);

      $permitidos = [
        'image/jpeg',
        'image/png',
        'image/webp'
      ];

      if (!in_array($mime, $permitidos)) {
        $erro .= "<p class='alerta'>Formato de imagem inválido</p>";
      }

      if (empty($erro)) {
        $nomeFoto = $_FILES['foto']['name'];
        $tmpFoto = $_FILES['foto']['tmp_name'];
        $extensao = pathinfo($nomeFoto, PATHINFO_EXTENSION);

        $novoNome = uniqid() . "." . $extensao;

        $caminho = "uploads/foto_itens/" . $novoNome;

        if (move_uploaded_file($tmpFoto, $caminho)) {
          $upload_stmt = "INSERT INTO itens (nome, categoria, descricao, id_proprietario, foto_item)
                          VALUES (?, ?, ?, ?, ?)";
          $stmt = $conn->prepare($upload_stmt);
          $stmt->bind_param("sssss", $nome, $categoriaSelecionada, $descricao, $id_usuario, $caminho);

          if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
          } else {
            $erro .= "<p class='alerta'>Erro no cadastro: {$stmt->error}</p>";
          }
        } else {
          $erro .= "<p class='alerta'>Erro ao salvar a imagem.</p>";
        }
      }
    }
  }
}
include("header.php");
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
  <form action="adicionarItens.php" method="post" enctype="multipart/form-data">
    <label>Nome: </label>
    <input type="text" name="nome" id="nome" required><br>
    <label>Descrição: </label>
    <input type="text" name="descricao" id="descricao" required><br>
    <label>Categoria: </label>
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
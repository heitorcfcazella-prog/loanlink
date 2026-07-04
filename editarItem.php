<?php
include("db.php");
include("header.php");

$id_item = $_GET['id_item'];

//pegar nome e descricao
$stmt = $conn->prepare("SELECT id_item, nome, descricao FROM itens WHERE id_item = ?");
$stmt->bind_param("i", $id_item);
$stmt->execute();

$result = $stmt->get_result();
$item = $result->fetch_assoc();

//query para as categorias
$categorias_stmt = $conn->prepare("
                      SELECT COLUMN_TYPE
                      FROM INFORMATION_SCHEMA.COLUMNS
                      WHERE TABLE_SCHEMA = DATABASE ()
                        AND TABLE_NAME = 'itens'
                        AND COLUMN_NAME = 'categoria'
  ");

$categorias_stmt->execute();
$result = $categorias_stmt->get_result();
$row = $result->fetch_assoc();

preg_match("/^enum\((.*)\)$/", $row['COLUMN_TYPE'], $matches);

$categorias = str_getcsv($matches[1], ',', "'");
$categorias_stmt->close();

//atualizar no banco de dados

if (isset($_POST['atualizar'])) {
  $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
  $descricao = filter_input(INPUT_POST, "descricao", FILTER_SANITIZE_SPECIAL_CHARS);
  $categoriaSelecionada = filter_input(INPUT_POST, "categoria", FILTER_SANITIZE_SPECIAL_CHARS);

  $atualizar_stmt = "UPDATE itens
                    SET nome = ?, descricao = ?, categoria = ?
                    WHERE id_item = ?";


  $stmt = $conn->prepare($atualizar_stmt);
  $stmt->bind_param("sssi", $nome, $descricao, $categoriaSelecionada, $id_item);
  $stmt->execute();
  $stmt->close();

  header("Location: dashboard.php");
  exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Item</title>
</head>

<body>
  <form action="editarItem.php?id_item=<?= $item['id_item'] ?>" method="post">
    <input type="hidden" name="id_item" value="<?= $item['id_item'] ?>">
    <label>Nome: </label>
    <input type="text" name="nome" value="<?= htmlspecialchars($item['nome'])  ?>"><br>
    <label>Categoria: </label>
    <select name="categoria">
      <?php foreach ($categorias as $categoria): ?>
        <option value="<?= htmlspecialchars($categoria) ?>">
          <?= htmlspecialchars($categoria) ?>
        </option>
      <?php endforeach; ?>
    </select><br>
    <label>Descrição: </label>
    <input type="text" name="descricao" value="<?= htmlspecialchars($item['descricao'])  ?>"><br>
    <button type="submit" name="atualizar">Salvar Alterações</button>
  </form>
</body>

</html>
<?php 
include('footer.html');
?>
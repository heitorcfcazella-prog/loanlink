<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $erro = "";

  $usuario = filter_input(INPUT_POST, "usuario", FILTER_SANITIZE_SPECIAL_CHARS);
  $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
  $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');

  //verificar email/telefone/usuario está duplicado
  $query = "SELECT id_usuario, email, telefone, nome
              FROM usuarios
              WHERE email = ? OR telefone = ? OR nome = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("sss", $email, $telefone, $usuario);
  $stmt->execute();

  $resultado = $stmt->get_result();

  if ($resultado->num_rows > 0) {
    $analise = $resultado->fetch_assoc();

    if ($analise['email'] == $email) {
      $erro .= "<p class='alerta'>E-mail já cadastrado!</p>";
    }

    if ($analise['telefone'] == $telefone) {
      $erro .= "<p class='alerta'>Telefone já cadastrado!</p>";
    }

    if ($analise['nome'] == $usuario) {
      $erro .= "<p class='alerta'>Nome/Usuário já cadastrado!</p>";
    }
  } else {
    // verificação de senha
    $senha = $_POST["senha"] ?? '';
    $senhaConfirmar = $_POST["senhaConfirmar"] ?? '';

    if ($senha !== $senhaConfirmar) {
      $erro .= "<p class='alerta'>Senhas diferentes!</p>";
    }

    //cadastro

    //foto de perfil
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

        $caminho = "uploads/foto_usuarios/" . $novoNome;

        //verificar se está salvo
        if (move_uploaded_file($tmpFoto, $caminho)) {
          //senha
          $hash = password_hash($senha, PASSWORD_DEFAULT);

          //query para por no banco
          $query = "INSERT INTO usuarios (nome, telefone, email, senha, foto_perfil) VALUES (?, ?, ?, ?, ?)";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("sssss", $usuario, $telefone, $email, $hash, $caminho);

          if ($stmt->execute()) {
            header("Location: login.php");
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
include("header.html");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página de Cadastro</title>
</head>

<body>
  <h1>Página de Cadastro</h1>
  <form action="cadastro.php" method="post" enctype="multipart/form-data">
    <label>Usuário: </label>
    <input type="text" name="usuario" id="usuario" required><br>
    <label>E-mail: </label>
    <input type="email" name="email" id="email" required><br>
    <label>Telefone: </label>
    <input type="tel" name="telefone" id="telefone" pattern="\(\d{2}\)\s\d{5}-\d{4}" required><br>
    <label>Senha: </label>
    <input type="password" name="senha" id="senha" required><br>
    <label>Confirmar senha: </label>
    <input type="password" name="senhaConfirmar" id="senhaConfirmar" required><br>
    <label>Escolha uma foto:</label><br>
    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp"><br>

    <input type="submit" name="enviar" value="Cadastrar"><br>
    <?php if (!empty($erro)): ?>
      <p class="erro"><?= $erro ?></p>
    <?php endif; ?>
  </form>
  <a href="login.php">Já tem conta? Acesse aqui!</a>
</body>

</html>
<?php
include("footer.html");
?>
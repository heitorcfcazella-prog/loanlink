<?php
session_start();
include("db.php");
if (isset($_SESSION['id_usuario'])) {
  header("Location: dashboard.php");
  exit;
} else {

  $erro = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    //verifica se o usuario existe

    $query = "SELECT id_usuario, nome, email, senha, foto_perfil
                FROM usuarios
                WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
      $erro = "Não existe um usuário com esse e-mail.";
    } else {
      //verificação de senha
      $confirmaBanco = $resultado->fetch_assoc();
      $hashBanco = $confirmaBanco['senha'];

      if (password_verify($senha, $hashBanco)) {

        //criar sessao
        session_regenerate_id(true);

        $_SESSION['id_usuario'] = $confirmaBanco['id_usuario'];
        $_SESSION['nome'] = $confirmaBanco['nome'];
        $_SESSION['email'] = $confirmaBanco['email'];
        $_SESSION['foto_perfil'] = $confirmaBanco['foto_perfil'];
        header("Location: dashboard.php");
        exit;
      } else {
        $erro = "Senha incorreta.";
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
  <title>Página de Login</title>
</head>

<body>
  <h1>Página de Login</h1>

  <form action="login.php" method="post">
    <label>E-mail: </label>
    <input type="text" name="email" id="email" required><br>
    <label>Senha: </label>
    <input type="password" name="senha" id="senha" required><br>
    <input type="submit" name="enviar" value="Logar"><br>
    <?php if (!empty($erro)): ?>
      <p class="erro"><?= $erro ?></p>
    <?php endif; ?>
  </form>
  <a href="cadastro.php">Não tem conta? Cadastre-se aqui!</a>
</body>

</html>
<?php
include("footer.html");
?>
<?php
session_start();
include("db.php");
if (isset($_SESSION['id_usuario']) && !empty($_SESSION['foto_perfil'])) {
  $imagem = '<a href="dashboard.php" style="float: right;"> <img src="' . htmlspecialchars($_SESSION['foto_perfil']) . '" alt="Perfil" style="height:40px; width:40px; border-radius:50%;"></a>';
} else {
  $imagem = '<a href="login.php" style="float: right;">Logar</a>';
}


?>
<header>
  <a href="main.php">Área de trocas</a>
  <a href="dashboard.php">Área do Usuário</a>
  <a href="adicionarItens.php">Adicionar Itens</a>
  <a href="historico.php">Histórico</a>
  <?= $imagem ?>
  <hr>
</header>
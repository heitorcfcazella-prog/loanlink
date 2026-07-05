<?php

$bd_server = "SEU_HOST";
$bd_usuario = "SEU_USUARIO";
$bd_senha = "SUA_SENHA";
$bd_nome = "SEU_BANCO";

  try {
    
  $conn = mysqli_connect( $bd_server, 
                          $bd_usuario, 
                          $bd_senha, 
                          $bd_nome);
  } catch(mysqli_sql_exception) {
    echo "Erro de conexão <br> ";
  }

?>

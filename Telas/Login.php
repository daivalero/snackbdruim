<?php
namespace PHP\Modelo;
require_once('../DAO/Conexao.php');
require_once('../Cliente.php');

use PHP\Modelo\DAO\Conexao;
use PHP\Modelo\DAO\Cliente;

session_start(); // Iniciar a sessão no início do arquivo

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conexao = new Conexao();
    $conn = $conexao->conectar();

    if (!$conn) {
        die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM clientes WHERE email = ?";  
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Erro ao preparar a consulta: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($password == $user['senha']) {
            // Armazenar o id do cliente na sessão
            $_SESSION['cliente_id'] = $user['codigo']; // 'codigo' é o id do usuário
            $_SESSION['cliente_nome'] = $user['nome']; // Armazenar o nome do cliente na sessão
            
            // Carregar o carrinho do banco de dados
            $sqlCarrinho = "SELECT * FROM itens_carrinho WHERE carrinho_id = (SELECT id FROM carrinho WHERE cliente_id = ?)";
            $stmtCarrinho = mysqli_prepare($conn, $sqlCarrinho);
            mysqli_stmt_bind_param($stmtCarrinho, 'i', $user['codigo']);
            mysqli_stmt_execute($stmtCarrinho);
            $resultCarrinho = mysqli_stmt_get_result($stmtCarrinho);

            $carrinho = [];
            while ($item = mysqli_fetch_assoc($resultCarrinho)) {
                $carrinho[] = $item;
            }
            $_SESSION['carrinho'] = $carrinho; // Armazenar o carrinho na sessão

            echo "<p style='color: green;'>Login realizado com sucesso!</p>";
            header('Location: ../Telas/IndexCliente.php');
            exit;
        } else {
            echo "<p style='color: red;'>Senha inválida!</p>";
        }
    } else {
        echo "<p style='color: red;'>Email não cadastrado!</p>";
    }
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" required>
            <label for="password">Senha:</label>
            <input type="password" name="password" id="password" required>
            <div class="register-link">
                <p>Não tem conta? <a href="CadastroCliente.php">Cadastre-se</a></p>
            </div>
            <div class="register-link">
                <a href="LoginFuncionario.php">Sou Funcionário</a>
            </div>
            <button type="submit">Entrar</button>
        </form>
        <a href="index.php#cardapio" class="btn-voltar">
            <button type="button">Voltar</button>
        </a>
    </div>
</body>
</html>
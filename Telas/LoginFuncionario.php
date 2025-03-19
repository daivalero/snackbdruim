<?php
namespace PHP\Modelo;
require_once('../DAO/Conexao.php');
require_once('../Funcionario.php');

use PHP\Modelo\DAO\Conexao;
use PHP\Modelo\DAO\Funcionario;

session_start(); // Iniciar a sessão no início do arquivo

if (isset($_POST['codigo_unico']) && isset($_POST['email']) && isset($_POST['password'])) {
    $codigoUnico = $_POST['codigo_unico'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conexao = new Conexao();
    $conn = $conexao->conectar();

    if (!$conn) {
        die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
    }

    // Verificar se o código único, email e senha são corretos
    $sql = "SELECT * FROM funcionario WHERE codigo_unico = ? AND email = ? AND senha = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Erro ao preparar a consulta: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'sss', $codigoUnico, $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Armazenar o id do funcionário na sessão
        $_SESSION['funcionario_id'] = $user['id']; // 'id' é o id do funcionário
        $_SESSION['funcionario_nome'] = $user['nome']; // Armazenar o nome do funcionário na sessão
        $_SESSION['is_admin'] = $user['is_admin']; // Armazenar se é admin na sessão
        
        // Redirecionamento após login bem-sucedido
        if ($user['is_admin']) {
            header('Location: ../Telas/AreaAdm.php');
        } else {
            header('Location: ../Telas/AreaFuncionario.php');
        }
        exit();  // Certifique-se de que o script pare após o redirecionamento
    } else {
        echo "<p style='color: red;'>Credenciais inválidas!</p>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Funcionário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #fff;
            color: black;
        }
            
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            width: 400px;
            }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #DF8B03;
        }
        label {
            display: block;
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
            background-color: white;
            color: black;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }
        .register-link {
            margin-top: 10px;
            font-size: 14px;
        }
        .register-link a {
            color: #DF8B03;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #DF8B03;
            border: none;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #c97a02;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login <samp>Funcionario</h2></samp>
        <form method="POST">

            <label for="codigo_unico">Código Único:</label>
            <input type="text" name="codigo_unico" id="codigo_unico" required>

            <label for="email">Email:</label>
            <input type="text" name="email" id="email" required>

            <label for="password">Senha:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Entrar</button>
        </form>

        <a href="index.php#cardapio" class="btn-voltar">
            <button type="button">Voltar</button>
        </a>
    </div>
</body>
</html>
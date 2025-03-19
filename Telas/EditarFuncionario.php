<?php
namespace PHP\Modelo;
require_once('../DAO/Conexao.php');
require_once('../Funcionario.php');

use PHP\Modelo\DAO\Conexao;
use PHP\Modelo\DAO\Funcionario;

// Verificar se o ID foi passado corretamente pela URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Conectar ao banco de dados
    $conexao = new Conexao();
    $conn = $conexao->conectar();

    if (!$conn) {
        die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
    }

    // Buscar o funcionário no banco de dados com o ID fornecido
    $sql = "SELECT * FROM funcionario WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Verificar se o funcionário foi encontrado
    if (mysqli_num_rows($result) > 0) {
        $funcionario = mysqli_fetch_assoc($result);
    } else {
        echo "<p style='color: red;'>Funcionário não encontrado com o ID: " . $id . "</p>";
        exit();
    }

    // Se o formulário for enviado, atualizar os dados do funcionário
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Atualizando o funcionário no banco de dados
        $sqlUpdate = "UPDATE funcionario SET nome = ?, email = ?, senha = ? WHERE id = ?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, 'sssi', $nome, $email, $senha, $id);
        mysqli_stmt_execute($stmtUpdate);

        // Verificar se a atualização foi bem-sucedida
        if (mysqli_stmt_affected_rows($stmtUpdate) > 0) {
            echo "<p style='color: green;'>Funcionário atualizado com sucesso!</p>";
            header('Location: Funcionarios.php'); // Redireciona para a lista de funcionários
            exit();
        } else {
            echo "<p style='color: red;'>Erro ao atualizar os dados do funcionário.</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário</title>
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
            background-color: #f4f4f4;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Funcionário</h2>

        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?php echo isset($funcionario['nome']) ? $funcionario['nome'] : ''; ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo isset($funcionario['email']) ? $funcionario['email'] : ''; ?>" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" value="<?php echo isset($funcionario['senha']) ? $funcionario['senha'] : ''; ?>" required>

            <button type="submit">Atualizar</button>
        </form>

        <div class="message">
            <?php
            // Exibir mensagens de sucesso ou erro
            if (isset($funcionario)) {
                if (mysqli_num_rows($result) > 0) {
                    echo "<p class='success'>Preencha os campos para atualizar o funcionário.</p>";
                } else {
                    echo "<p class='error'>Funcionário não encontrado.</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>

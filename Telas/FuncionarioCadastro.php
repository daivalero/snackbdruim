<?php
namespace PHP\Modelo;
require_once('..\DAO\Conexao.php');
require_once('..\DAO\Inserir.php');
use PHP\Modelo\DAO\Conexao;
use PHP\Modelo\DAO\Inserir;

session_start(); // Inicia a sessão para armazenar a mensagem
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionário</title>
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
            background-color: rgb(255, 255, 255);
            color: black;
            position: relative;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
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
        button {
            width: 100%;
            padding: 12px;
            background: #004A8D;
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
            background: #e64a19;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }

        /* Estilizando o modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            margin: 15% auto;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }
        .close {
            color: red;
            float: right;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>

    <script>
        function validarSenha() {
            var senha = document.getElementById("senha").value;
            var confirmarSenha = document.getElementById("confirmarSenha").value;
            var mensagemErro = document.getElementById("mensagemErro");

            if (senha !== confirmarSenha) {
                mensagemErro.innerHTML = "As senhas não coincidem!";
                return false;
            }
            return true;
        }

        function mostrarModal() {
            var modal = document.getElementById("modalSucesso");
            modal.style.display = "block";
            
            // Fecha automaticamente após 3 segundos
            setTimeout(function () {
                modal.style.display = "none";
            }, 3000);
        }
    </script>
</head>
<body>

    <div class="container">
        <h2>Cadastro Funcionário</h2>

        <form method="POST" onsubmit="return validarSenha();">
            <label>ID:</label>
            <input type="text" name="Tid" required>

            <label>Código Único:</label>
            <input type="text" name="tCodigo" required>

            <label>Nome:</label>
            <input type="text" name="tNome" required>

            <label>Email:</label>
            <input type="email" name="tEmail" required>

            <label>Senha:</label>
            <input type="password" name="tSenha" id="senha" required>

            <label>Confirme sua Senha:</label>
            <input type="password" id="confirmarSenha" required>

            <p id="mensagemErro" class="error-message"></p>

            <a href="Funcionarios.php">
                <button type="submit">Cadastrar</button>
            </a>
        </form>

        <a href="Funcionarios.php"><button type="button">Voltar</button></a>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['Tid'])) {
            $id = trim($_POST['Tid']);
            $codigo = trim($_POST['tCodigo']);
            $nome = trim($_POST['tNome']);
            $email = trim($_POST['tEmail']);
            $senha = $_POST['tSenha'];

            $conexao = new Conexao(); 
            $inserir = new Inserir();

            // Tenta cadastrar o funcionário e verifica se deu certo
            $resultado = $inserir->cadastrarFuncionario($conexao, $id, $codigo, $nome, $email, $senha);
            
            if ($resultado == "Cadastro realizado com sucesso!") {
                $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso!";
                header("Location: Funcionarios.php"); // Redireciona para a página de funcionários
                exit();
            }
        }

        // Exibir a mensagem de sucesso se houver
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo "<script>window.onload = function() { mostrarModal(); };</script>";
            unset($_SESSION['mensagem_sucesso']); // Remove a mensagem após exibir
        }
        ?>
    </div>

    <!-- Modal de Sucesso -->
    <div id="modalSucesso" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('modalSucesso').style.display='none'">&times;</span>
            <p>Cadastro realizado com sucesso!</p>
        </div>
    </div>
</body>
</html>

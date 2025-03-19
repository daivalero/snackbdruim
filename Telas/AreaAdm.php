<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <title>Área do Administrador</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header("Location: LoginFuncionario.php");
    exit();
}
?>

<header>
    <div class="container">
        <div class="img">
            <a href="IndexCliente.php">
                <img src="../images/logo_snack_fast.png" alt="Logotipo do site">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="AreaFuncionarioADM.php" class="btnvoltar"><i class="fas fa-box"></i> Pedidos</a></li>
                <li><a href="produtos.php" class="btnvoltar"><i class="fas fa-tags"></i> Produtos</a></li>
                <li><a href="feedback.php" class="btnvoltar"><i class="fas fa-comments"></i> Feedback</a></li>
                <li><a href="Funcionarios.php" class="btnvoltar"><i class="fas fa-users"></i> Funcionários</a></li>
            </ul>
        </nav>
        <a href="LoginFuncionario.php">
            <button class="btnvoltar">Sair</button>
        </a>
    </div>
</header>

<main>
    <div class="container">
        <h1>Área do Administrador</h1>
        <div class="dashboard">
            <div class="card">
                <div class="card-icon"><i class="fas fa-box"></i></div>
                <h2>Pedidos</h2>
                <p>Gerencie os pedidos dos clientes.</p>
                <a href="AreaFuncionarioADM.php" class="btn">Ver Pedidos</a>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-tags"></i></div>
                <h2>Produtos</h2>
                <p>Gerencie os produtos disponíveis.</p>
                <a href="produtos.php" class="btn">Ver Produtos</a>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-comments"></i></div>
                <h2>Feedback</h2>
                <p>Veja o feedback dos clientes.</p>
                <a href="feedback.php" class="btn">Ver Feedback</a>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h2>Funcionários</h2>
                <p>Gerencie os funcionários.</p>
                <a href="Funcionarios.php" class="btn">Ver Funcionários</a>
            </div>
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Snack Fast. Todos os direitos reservados.</p>
    </div>
</footer>

</body>
</html>
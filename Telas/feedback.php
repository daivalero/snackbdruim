<?php
namespace PHP\Modelo;
require_once('../DAO/Conexao.php');

use PHP\Modelo\DAO\Conexao;

$conexao = new Conexao();
$conn = $conexao->conectar();

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Consultar todas as mensagens de contato
$sql = "SELECT * FROM contatos ORDER BY data_envio DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Erro na consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens de Contato</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/contatos.css">
</head>
<body>
<header>
    <div class="container">
        <div class="img">
            <a href="IndexCliente.php">
                <img src="../images/logo_snack_fast.png" alt="Logotipo do site">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="AreaAdm.php" class="btnvoltar"><i class="fas fa-box"></i>Menu</a></li>
            </ul>
        </nav>
    </div>
</header>
<main class="container">
    <h1>Mensagens de Contato</h1>
    <?php if (mysqli_num_rows($result) > 0) { ?>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Mensagem</th>
                    <th>Data de Envio</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['nome'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['mensagem'] ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($row['data_envio'])) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center text-danger">Nenhuma mensagem de contato encontrada.</p>
    <?php } ?>
</main>
<footer>
    <div class="container">
        <p>&copy; 2025 Lista de Funcionários</p>
    </div>
</footer>
</body>
</html>

<?php mysqli_close($conn); ?>
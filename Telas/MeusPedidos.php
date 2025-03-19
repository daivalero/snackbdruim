<?php
namespace PHP\Modelo;
require_once('../DAO/Conexao.php');

use PHP\Modelo\DAO\Conexao;

session_start(); // Certifique-se de que a sessão está iniciada

$conexao = new Conexao();
$conn = $conexao->conectar();

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$cliente_id = $_SESSION['cliente_id']; // Supondo que o ID do cliente esteja armazenado na sessão

// Verificar se o pedido de cancelamento foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pedido_id'])) {
    $pedido_id = $_POST['pedido_id'];

    // Verificar se o pedido pertence ao cliente logado
    $sql = "SELECT * FROM agendamentos WHERE id = ? AND cliente_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $pedido_id, $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Pedido pertence ao cliente, pode ser cancelado
        $sql_delete = "DELETE FROM agendamentos WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $pedido_id);
        if ($stmt_delete->execute()) {
            $message = "Pedido cancelado com sucesso!";
        } else {
            $message = "Erro ao cancelar o pedido: " . $stmt_delete->error;
        }
        $stmt_delete->close();
    } else {
        $message = "Pedido não encontrado ou não pertence ao cliente.";
    }

    $stmt->close();
}

// Consultar os pedidos do cliente
$sql = "SELECT id, data, horario, produtos 
        FROM agendamentos 
        WHERE cliente_id = ? 
        ORDER BY data DESC, horario DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Erro na consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/cliente.css">
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
                <li><a href="IndexCliente.php" class="btnvoltar"><i class="fas fa-box"></i>Voltar</a></li>
            </ul>
        </nav>
    </div>
</header>
<main class="container">
    <h1>Meus Pedidos</h1>
    <?php if (isset($message)) { ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php } ?>
    <?php if ($result->num_rows > 0) { ?>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Produtos</th>
                    <th>Pagamento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                        <td><?= date('H:i', strtotime($row['horario'])) ?></td>
                        <td>
                            <?php 
                            $produtos = json_decode($row['produtos'], true);
                            if (is_array($produtos)) {
                                foreach ($produtos as $produto) {
                                    echo htmlspecialchars($produto['nome']) . "<br>";
                                    if (isset($produto['adicionais']) && is_array($produto['adicionais'])) {
                                        foreach ($produto['adicionais'] as $adicional) {
                                            echo " - " . htmlspecialchars($adicional['nome']) . "<br>";
                                        }
                                    }
                                }
                            } else {
                                echo "Nenhum produto";
                            }
                            ?>
                        </td>
                        <td>Pessoalmente</td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="pedido_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-danger">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center text-danger">Nenhum pedido encontrado.</p>
    <?php } ?>
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
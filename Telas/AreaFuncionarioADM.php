<?php
namespace PHP\Modelo;
require_once('../DAO/Conexao.php');

use PHP\Modelo\DAO\Conexao;

$conexao = new Conexao();
$conn = $conexao->conectar();

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Consultar todos os agendamentos
$sql = "SELECT a.id, a.data, a.horario, a.produtos, a.observacoes, c.nome AS cliente_nome 
        FROM agendamentos a 
        JOIN clientes c ON a.cliente_id = c.codigo 
        ORDER BY a.data DESC, a.horario DESC";
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
    <title>Agendamentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/agendamentos.css">
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
    <h1>Agendamentos</h1>
    <?php if (mysqli_num_rows($result) > 0) { ?>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Produtos</th>
                    <th>Quantidade</th>
                    <th>Observações</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $currentCliente = '';
                while ($row = mysqli_fetch_assoc($result)) { 
                    if ($currentCliente != $row['cliente_nome']) {
                        $currentCliente = $row['cliente_nome'];
                        ?>
                        <tr>
                            <td colspan="7" class="table-active"><strong><?= htmlspecialchars($currentCliente) ?></strong></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                        <td><?= date('H:i', strtotime($row['horario'])) ?></td>
                        <td>
                            <?php 
                            $produtos = json_decode($row['produtos'], true);
                            $total = 0;
                            if (is_array($produtos)) {
                                foreach ($produtos as $produto) {
                                    echo htmlspecialchars($produto['nome']) . "<br>";
                                    if (isset($produto['adicionais']) && is_array($produto['adicionais'])) {
                                        foreach ($produto['adicionais'] as $adicional) {
                                            echo " - " . htmlspecialchars($adicional['nome']) . " (" . number_format($adicional['preco'], 2, ',', '.') . ")<br>";
                                        }
                                    }
                                    $adicional_preco_total = isset($produto['adicionais']) ? array_sum(array_column($produto['adicionais'], 'preco')) : 0;
                                    $total += ($produto['preco'] + $adicional_preco_total) * $produto['quantidade'];
                                }
                            } else {
                                echo "Nenhum produto";
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if (is_array($produtos)) {
                                foreach ($produtos as $produto) {
                                    echo htmlspecialchars($produto['quantidade']) . "<br>";
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if (is_array($produtos)) {
                                foreach ($produtos as $produto) {
                                    echo htmlspecialchars($produto['observacoes']) . "<br>";
                                }
                            }
                            ?>
                        </td>
                        <td><?= number_format($total, 2, ',', '.') ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center text-danger">Nenhum agendamento encontrado.</p>
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
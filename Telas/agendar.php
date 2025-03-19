<?php
session_start();
include '../DAO/Conexao.php';

use PHP\Modelo\DAO\Conexao;

$conexao = new Conexao();
$conn = $conexao->conectar();

// Verifique se o cliente_id está definido na sessão
if (!isset($_SESSION['cliente_id'])) {
    // Redirecione para a página de login se o cliente não estiver logado
    header("Location: login.php");
    exit();
}

$cliente_id = $_SESSION['cliente_id'];

// Recuperar informações do cliente do banco de dados
$result_cliente = $conn->query("SELECT nome, email FROM clientes WHERE codigo = $cliente_id");
if (!$result_cliente) {
    echo "Erro na consulta SQL: " . $conn->error;
    exit();
}
$cliente = $result_cliente->fetch_assoc();

// Recuperar produtos do carrinho
$produtos = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];

// Calcular o valor total da compra
$total = 0;
foreach ($produtos as $produto) {
    $total += $produto['preco'] * $produto['quantidade'];
}

$mostrar_modal = false;
$error_message = '';
$data = '';
$horario = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'];
    $horario = $_POST['horario'];

    $current_date = new DateTime();
    $current_date->setTime(0, 0); // Define a hora para 00:00 para comparação correta
    $selected_date = DateTime::createFromFormat('Y-m-d', $data);
    $selected_date->setTime(0, 0); // Define a hora para 00:00 para comparação correta

    if ($selected_date === false) {
        $error_message = 'Data inválida. Por favor, insira uma data no formato correto.';
    } elseif ($selected_date->format('Y') != 2025) {
        $error_message = 'O ano deve ser 2025.';
    } elseif ($selected_date->format('m') != $current_date->format('m')) {
        $error_message = 'O mês deve ser o mês atual.';
    } elseif ($selected_date < $current_date) {
        $error_message = 'A data não pode ser anterior à data atual.';
    } else {
        // Verificar se o horário está entre 06:00 e 23:00
        $hora_minima = new DateTime('06:00');
        $hora_maxima = new DateTime('23:00');
        $hora_selecionada = DateTime::createFromFormat('H:i', $horario);

        if ($hora_selecionada < $hora_minima || $hora_selecionada > $hora_maxima) {
            $error_message = 'O horário deve estar entre 06:00 e 23:00.';
        } else {
            // Converter os produtos para um formato de texto (JSON)
            $produtos_json = json_encode($produtos);

            // Inserir o agendamento no banco de dados
            $stmt = $conn->prepare("INSERT INTO agendamentos (cliente_id, data, horario, produtos) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $cliente_id, $data, $horario, $produtos_json);
            $stmt->execute();

            // Limpar o carrinho após o agendamento
            unset($_SESSION['carrinho']);
            
            // Exibir o modal de pagamento
            $mostrar_modal = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/agenda.css"> <!-- Link para o arquivo CSS externo -->
</head>
<body>
<div class="container">
    <h1>Agendar Pedidos</h1>
    <form method="post" action="agendar.php">
        <div class="form-group info-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
            </svg>
            <p><?php echo $cliente['nome']; ?></p>
        </div>
        <div class="form-group info-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-at-fill" viewBox="0 0 16 16">
                <path d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2zm-2 9.8V4.698l5.803 3.546zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.5 4.5 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586zM16 9.671V4.697l-5.803 3.546.338.208A4.5 4.5 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671"/>
                <path d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791"/>
            </svg>
            <p><?php echo $cliente['email']; ?></p>
        </div>
        <div class="form-group info-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
            </svg>
            <input type="date" class="form-control" id="data" name="data" value="<?php echo htmlspecialchars($data); ?>" required>
            <?php if ($error_message): ?>
                <div class="text-danger mt-2"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group info-item">
            <img src="../images/Horas.png" alt="Horário" width="16" height="16">
            <input type="time" class="form-control" id="horario" name="horario" value="<?php echo htmlspecialchars($horario); ?>" required>
        </div>
        <h2>Produtos</h2>
        <ul>
            <?php
            if (!empty($produtos)) {
                foreach ($produtos as $produto) {
                    echo "<li>" . $produto['nome'] . " - Quantidade: " . $produto['quantidade'] . "</li>";
                }
            } else {
                echo "<li>Sem produtos selecionado.</li>";
            }
            ?>
        </ul>
        <p><strong>Valor Total: <span class="dinheiro">R$ <?php echo number_format($total, 2, ',', '.'); ?></span></strong></p>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btnvoltar">Confirmar Agendamento</button>
                <a href="carrinho.php">
                    <button type="button" class="btnvoltar">Voltar</button>
                </a>
            </div>
        </form>
    
</div>

<!-- Modal de Pagamento -->
<div class="modal fade" id="pagamentoModal" tabindex="-1" role="dialog" aria-labelledby="pagamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagamentoModalLabel">Pagamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p>Escaneie o QR Code para pagar:</p>
                <img src="../images/qrcode.png" alt="QR Code" class="img-fluid mb-3 qr-code">
                <p><a id="pagarPessoalmente" class="link-danger">Pagar Pessoalmente</a></p>
                <p><strong>Valor Total: <span class="dinheiro">R$ <?php echo number_format($total, 2, ',', '.'); ?></span></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btnfechar" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    <?php if ($mostrar_modal) { ?>
        $('#pagamentoModal').modal('show');
    <?php } ?>
    
    $('#pagarPessoalmente').click(function() {
        window.location.href = 'indexCliente.php';
    });
});
</script>

</body>
</html>
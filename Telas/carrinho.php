<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho Snack Fast</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/carrinho.css">
</head>
<body>
<div class="container">
    <h1>CARRINHO SNACK FAST</h1>
    <div class="box">
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Adicional</th>
                    <th>Preço</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php
session_start();
include '../DAO/Conexao.php';

use PHP\Modelo\DAO\Conexao;

$conexao = new Conexao();
$conn = $conexao->conectar();

if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM produtos WHERE id = $id");
    $produto = $result->fetch_assoc();

    $adicional_preco = 0;
    $adicional_nome = '';
    if (isset($_POST['adicional'])) {
        foreach ($_POST['adicional'] as $preco) {
            $adicional_preco += $preco;
        }
        $adicional_nome = implode(', ', array_map(function($preco) use ($conn) {
            $result = $conn->query("SELECT nome FROM adicionais WHERE preco = $preco");
            return $result->fetch_assoc()['nome'];
        }, $_POST['adicional']));
    }

    $item = array(
        "nome" => $produto['nome'],
        "preco" => $produto['preco'],
        "adicional_nome" => $adicional_nome,
        "adicional_preco" => $adicional_preco,
        "quantidade" => 1,
        "observacoes" => ''
    );

    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = array();
    }

    $_SESSION['carrinho'][] = $item;

    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        header("Location: carrinho.php");
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $index = $_GET['id'];
    if (isset($_SESSION['carrinho'][$index])) {
        unset($_SESSION['carrinho'][$index]);
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindex array
    }
    header("Location: carrinho.php");
    exit();
}

if (isset($_POST['save_observacoes'])) {
    $index = $_POST['id'];
    $observacoes = $_POST['observacoes'];
    $_SESSION['carrinho'][$index]['observacoes'] = $observacoes;
}

$total_geral = 0;
$total_quantidade = 0;
if (!empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $index => $produto) {
        $total_preco = ($produto['preco'] + $produto['adicional_preco']) * $produto['quantidade'];
        $total_geral += $total_preco;
        $total_quantidade += $produto['quantidade'];

        $observacoes = htmlspecialchars($produto['observacoes']);
        $limite = 15;
        if (strlen($observacoes) > $limite) {
            $observacoesTruncadas = substr($observacoes, 0, $limite) . "...";
        } else {
            $observacoesTruncadas = $observacoes;
        }

        $observacoesClass = empty($observacoes) ? 'observacoes-vazias' : ''; // Adiciona classe se estiver vazio

        echo "<tr>";
        echo "<td class='product-name'>" . htmlspecialchars($produto['nome']) . "</td>";
        echo "<td style='text-align: center;'>" . htmlspecialchars($produto['quantidade']) . "</td>";
        echo "<td>" . ($produto['adicional_nome'] != '' ? htmlspecialchars($produto['adicional_nome']) . " (+R$ <span class='green-price'>" . number_format($produto['adicional_preco'], 2, ',', '.') . "</span>)" : 'Nenhum') . "</td>";
        echo "<td class='price'>Preço: R$ " . number_format($total_preco, 2, ',', '.') . "</td>";
        echo "<td class='observacoes-cell " . $observacoesClass . "'>" . $observacoesTruncadas . " <a href='#' data-toggle='modal' data-target='#observacoesModal' data-index='" . $index . "' data-observacoes='" . htmlspecialchars($produto['observacoes']) . "'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil lapis-icone' viewBox='0 0 16 16'><path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/></svg></a></td>";
        echo "<td class='actions'><button class='btn btn-danger' onclick=\"window.location.href='carrinho.php?action=remove&id=" . $index . "'\">Excluir</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Seu carrinho está vazio.</td></tr>";
}
?>
            </tbody>
        </table>
    </div>

    <div class="box" style="display: flex; align-items: center; justify-content: space-between;">
        <div class="total">Total (<?php echo $total_quantidade; ?> Item): <span>R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></span></div>
        <div class="actions"><a class="btn btn-continuar" href="IndexCliente.php#cardapio">Continuar Comprando</a></div>
        <div class="actions"><button class="btn btn-agendar" onclick="verificarCarrinhoVazio()">Agendar</button></div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="observacoesModal" tabindex="-1" role="dialog" aria-labelledby="observacoesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="observacoesModalLabel">Editar Observações</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="observacoesForm" method="post" action="carrinho.php">
                        <div class="form-group">
                            <label for="observacoes">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                        </div>
                        <input type="hidden" id="produtoIndex" name="id">
                        <button type="submit" name="save_observacoes" class="btn btn-primary">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $('#observacoesModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var index = button.data('index');
            var observacoes = button.data('observacoes');
            var modal = $(this);
            modal.find('#produtoIndex').val(index);
            modal.find('#observacoes').val(observacoes);
        });

        function verificarCarrinhoVazio() {
            if (<?php echo empty($_SESSION['carrinho']) ? 'true' : 'false'; ?>) {
                alert('Não é possível agendar. Seu carrinho está vazio.');
                return false;
            } else {
                window.location.href = 'agendar.php';
            }
        }
    </script>
</body>
</html>
<?php
include('../DAO/Conexao.php');
use PHP\Modelo\DAO\Conexao;

// Criando a instância da classe Conexao
$conexao = new Conexao();
$conn = $conexao->conectar();

// Verificando se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro ao conectar ao banco de dados.");
}

// Consulta SQL para buscar os produtos
$sql = "SELECT * FROM produtos";
$result = mysqli_query($conn, $sql);

// Verificando se há produtos
if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Ação</th>
            </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $produtoId = $row['id']; // Capturando o ID do produto
        $nome = $row['nome'];
        $preco = $row['preco'];

        echo "<tr>
                <td>{$nome}</td>
                <td>R$ " . number_format($preco, 2, ',', '.') . "</td>
                <td>
                    <button class='btn-adicionar' data-id='{$produtoId}' data-nome='{$nome}' data-preco='{$preco}'>
                        Adicionar ao Carrinho
                    </button>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum produto encontrado.</p>";
}

// Fechando a conexão
mysqli_close($conn);
?>

<!-- Script para adicionar ao carrinho via AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $(".btn-adicionar").click(function() {
        var produtoId = $(this).data("id");
        var nomeProduto = $(this).data("nome");
        var precoProduto = $(this).data("preco");

        $.ajax({
            url: 'Carrinhoo.php',
            method: 'POST',
            data: {
                produto_id: produtoId,
                nome_produto: nomeProduto,
                preco_final: precoProduto,
                quantidade: 1
            },
            success: function(response) {
                alert("Produto adicionado ao carrinho com sucesso!");
            },
            error: function() {
                alert("Erro ao adicionar o produto ao carrinho.");
            }
        });
    });
});
</script>

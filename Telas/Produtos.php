<?php
include '../DAO/Conexao.php';

use PHP\Modelo\DAO\Conexao;

$conexao = new Conexao();
$conn = $conexao->conectar();

if (isset($_POST['add_product'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];
    $imagem = $_FILES['imagem']['name'];
    $target = "../images/" . basename($imagem);

    $sql = "INSERT INTO produtos (nome, descricao, preco, categoria, imagem) VALUES ('$nome', '$descricao', '$preco', '$categoria', '$imagem')";
    if (@$conn->query($sql) === TRUE) {
        $produto_id = $conn->insert_id;
        if (isset($_POST['adicional'])) {
            foreach ($_POST['adicional'] as $adicional_id) {
                @$conn->query("INSERT INTO produto_adicionais (produto_id, adicional_id) VALUES ($produto_id, $adicional_id)");
            }
        }
        move_uploaded_file($_FILES['imagem']['tmp_name'], $target);
        echo "Produto adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar produto.";
    }
}

// Função para excluir produto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    @$conn->query("DELETE FROM produto_adicionais WHERE produto_id=$id");
    $sql = "DELETE FROM produtos WHERE id=$id";
    if (@$conn->query($sql) === TRUE) {
        echo "Produto excluído com sucesso!";
    } else {
        echo "Erro ao excluir produto.";
    }
}

// Função para atualizar produto
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];
    $imagem = $_FILES['imagem']['name'];
    $target = "../images/" . basename($imagem);

    if ($imagem) {
        $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco', categoria='$categoria', imagem='$imagem' WHERE id=$id";
        move_uploaded_file($_FILES['imagem']['tmp_name'], $target);
    } else {
        $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco', categoria='$categoria' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        $conn->query("DELETE FROM produto_adicionais WHERE produto_id=$id");
        if (isset($_POST['adicional'])) {
            foreach ($_POST['adicional'] as $adicional_id) {
                $conn->query("INSERT INTO produto_adicionais (produto_id, adicional_id) VALUES ($id, $adicional_id)");
            }
        }
        echo "Produto atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar produto: " . $conn->error;
    }
}

// Função para obter adicionais
function getAdicionais($conn) {
    $result = $conn->query("SELECT * FROM adicionais");
    $adicionais = [];
    while ($row = $result->fetch_assoc()) {
        $adicionais[] = $row;
    }
    return $adicionais;
}

$adicionais = getAdicionais($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/produto.css">
    <title>Gerenciar Produtos</title>
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

<main>
    <div class="container">
        <h1>Gerenciar Produtos</h1>

        <!-- Seção de formulários -->
        <div class="form-section">
            <div class="tabs">
                <button class="tab-link active" onclick="openTab(event, 'addProduct')">Adicionar Produto</button>
                <button class="tab-link" onclick="openTab(event, 'editProduct')">Editar Produto</button>
            </div>

            <!-- Formulário para adicionar produto -->
            <div id="addProduct" class="tab-content active">
                <form action="produtos.php" method="post" enctype="multipart/form-data" class="form-produto">
                    <h2>Adicionar Produto</h2>
                    <input type="text" name="nome" placeholder="Nome do Produto" required>
                    <textarea name="descricao" placeholder="Descrição do Produto" required></textarea>
                    <input type="number" name="preco" placeholder="Preço do Produto" required>
                    <select name="categoria" class="select-categoria" required>
                        <option value="hotdog">Hotdog</option>
                        <option value="hamburguer">Hambúrguer</option>
                        <option value="sanduiche">Sanduíche</option>
                        <option value="bebida">Bebida</option>
                    </select>

                    <input type="file" name="imagem" class="input-arquivo" required>
                    <h3>Adicionais</h3>
                    <?php foreach ($adicionais as $adicional): ?>
                        <div class="checkbox-container">
                            <input type="checkbox" name="adicional[]" value="<?php echo $adicional['id']; ?>" id="adicional-<?php echo $adicional['id']; ?>">
                            <label for="adicional-<?php echo $adicional['id']; ?>"><?php echo $adicional['nome']; ?> (+R$ <?php echo number_format($adicional['preco'], 2, ',', '.'); ?>)</label>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" name="add_product" class="btnvoltar">Adicionar Produto</button>
                </form>
            </div>

            <!-- Formulário para editar produto -->
            <div id="editProduct" class="tab-content">
                <?php
                if (isset($_GET['edit'])) {
                    $id = $_GET['edit'];
                    $result = $conn->query("SELECT * FROM produtos WHERE id=$id");
                    $row = $result->fetch_assoc();
                    $produto_adicionais = $conn->query("SELECT adicional_id FROM produto_adicionais WHERE produto_id=$id");
                    $adicionais_ids = [];
                    while ($adicional = $produto_adicionais->fetch_assoc()) {
                        $adicionais_ids[] = $adicional['adicional_id'];
                    }
                ?>
                <form action="produtos.php" method="post" enctype="multipart/form-data" class="form-produto">
                    <h2>Editar Produto</h2>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="nome" value="<?php echo $row['nome']; ?>" required>
                    <textarea name="descricao" required><?php echo $row['descricao']; ?></textarea>
                    <input type="number" name="preco" value="<?php echo $row['preco']; ?>" required>
                    <select name="categoria" required>
                        <option value="hotdog" <?php if ($row['categoria'] == 'hotdog') echo 'selected'; ?>>Hotdog</option>
                        <option value="hamburguer" <?php if ($row['categoria'] == 'hamburguer') echo 'selected'; ?>>Hambúrguer</option>
                        <option value="sanduiche" <?php if ($row['categoria'] == 'sanduiche') echo 'selected'; ?>>Sanduíche</option>
                        <option value="bebida" <?php if ($row['categoria'] == 'bebida') echo 'selected'; ?>>Bebida</option>
                    </select>
                    <input type="file" name="imagem">
                    <h3>Adicionais</h3>
                    <?php foreach ($adicionais as $adicional): ?>
                        <div class="checkbox-container">
                            <input type="checkbox" name="adicional[]" value="<?php echo $adicional['id']; ?>" id="adicional-<?php echo $adicional['id']; ?>" <?php if (in_array($adicional['id'], $adicionais_ids)) echo 'checked'; ?>>
                            <label for="adicional-<?php echo $adicional['id']; ?>"><?php echo $adicional['nome']; ?> (+R$ <?php echo number_format($adicional['preco'], 2, ',', '.'); ?>)</label>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" name="update_product" class="btnvoltar">Atualizar Produto</button>
                </form>
                <?php } ?>
            </div>
        </div>

        <!-- Tabela de produtos -->
        <h2>Produtos Existentes</h2>
        <table class="tabela-produtos">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Categoria</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM produtos");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='../images/" . $row['imagem'] . "' alt='" . $row['imagem'] . "' alt='" . $row['nome'] . "' width='50'></td>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['descricao'] . "</td>";
                    echo "<td>R$ " . $row['preco'] . "</td>";
                    echo "<td>" . $row['categoria'] . "</td>";
                    echo "<td>";
                    echo "<a href='produtos.php?edit=" . $row['id'] . "' class='btn'>Editar</a>";
                    echo "<a href='produtos.php?delete=" . $row['id'] . "' class='btn'>Excluir</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Snack Fast. Todos os direitos reservados.</p>
    </div>
</footer>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>

</body>
</html>
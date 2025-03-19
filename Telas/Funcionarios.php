<?php
namespace PHP\Modelo;
require_once('../DAO/Conexao.php');
require_once('../Funcionario.php');

use PHP\Modelo\DAO\Conexao;
use PHP\Modelo\DAO\Funcionario;

$conexao = new Conexao();
$conn = $conexao->conectar();

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Inserir novo funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_funcionario'])) {
    $codigoUnico = $_POST['codigo_unico'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    // Verificar se o código único já existe
    $sqlCheck = "SELECT * FROM funcionario WHERE codigo_unico = ?";
    $stmtCheck = mysqli_prepare($conn, $sqlCheck);

    if ($stmtCheck) {
        mysqli_stmt_bind_param($stmtCheck, 's', $codigoUnico);
        mysqli_stmt_execute($stmtCheck);
        $resultCheck = mysqli_stmt_get_result($stmtCheck);

        if (mysqli_num_rows($resultCheck) > 0) {
            echo "<p style='color: red;'>Código único já existe. Por favor, escolha outro.</p>";
        } else {
            $sqlInsert = "INSERT INTO funcionario (codigo_unico, nome, email, senha, is_admin) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = mysqli_prepare($conn, $sqlInsert);

            if ($stmtInsert) {
                mysqli_stmt_bind_param($stmtInsert, 'ssssi', $codigoUnico, $nome, $email, $senha, $isAdmin);
                mysqli_stmt_execute($stmtInsert);
                mysqli_stmt_close($stmtInsert);
            } else {
                die("Erro ao preparar a consulta de inserção: " . mysqli_error($conn));
            }
        }

        mysqli_stmt_close($stmtCheck);
    } else {
        die("Erro ao preparar a consulta de verificação: " . mysqli_error($conn));
    }
}

// Atualizar funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_funcionario'])) {
    $id = $_POST['id'];
    $codigoUnico = $_POST['codigo_unico'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    $sqlUpdate = "UPDATE funcionario SET codigo_unico = ?, nome = ?, email = ?, senha = ?, is_admin = ? WHERE id = ?";
    $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);

    if ($stmtUpdate) {
        mysqli_stmt_bind_param($stmtUpdate, 'ssssii', $codigoUnico, $nome, $email, $senha, $isAdmin, $id);
        mysqli_stmt_execute($stmtUpdate);
        mysqli_stmt_close($stmtUpdate);
    } else {
        die("Erro ao preparar a consulta de atualização: " . mysqli_error($conn));
    }
}

// Consultar todos os funcionários
$sql = "SELECT * FROM funcionario";
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
    <title>Lista de Funcionários</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/listafuncionario.css">
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
    <?php if (mysqli_num_rows($result) > 0) { ?>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Código Único</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['codigo_unico'] ?></td>
                        <td><?= $row['nome'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['is_admin'] ? 'Administrador' : 'Funcionário' ?></td>
                        <td>
                            <button class="btn" data-bs-toggle="modal" data-bs-target="#updateFuncionarioModal" data-id="<?= $row['id'] ?>" data-codigo="<?= $row['codigo_unico'] ?>" data-nome="<?= $row['nome'] ?>" data-email="<?= $row['email'] ?>" data-senha="<?= $row['senha'] ?>" data-admin="<?= $row['is_admin'] ?>">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <a href="ExcluirFuncionario.php?id=<?= $row['id'] ?>" class="btn" onclick="return confirm('Tem certeza que deseja excluir?');">
                                <i class="bi bi-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center text-danger">Nenhum funcionário encontrado.</p>
    <?php } ?>

    <div class="d-flex justify-content-between">
        <button class="btn" data-bs-toggle="modal" data-bs-target="#addFuncionarioModal">
            <i class="bi bi-person-plus"></i> Adicionar Funcionário
        </button>
    </div>

    <!-- Modal para adicionar funcionário -->
    <div class="modal fade" id="addFuncionarioModal" tabindex="-1" aria-labelledby="addFuncionarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFuncionarioModalLabel">Adicionar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="add_funcionario" value="1">
                        <div class="mb-3">
                            <label for="codigo_unico" class="form-label">Código Único</label>
                            <input type="text" class="form-control" id="codigo_unico" name="codigo_unico" required>
                        </div>
                        <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin">
                            <label class="form-check-label" for="is_admin">Administrador</label>
                        </div>
                        <button type="submit" class="btnvoltar">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para atualizar funcionário -->
    <!-- Modal para atualizar funcionário -->
<div class="modal fade" id="updateFuncionarioModal" tabindex="-1" aria-labelledby="updateFuncionarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateFuncionarioModalLabel">Atualizar Funcionário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="update_funcionario" value="1">
                    <input type="hidden" id="update_id" name="id">
                    <div class="mb-3">
                        <label for="update_codigo_unico" class="form-label">Código Único</label>
                        <input type="text" class="form-control" id="update_codigo_unico" name="codigo_unico" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="update_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="update_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="update_senha" name="senha" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="update_is_admin" name="is_admin">
                        <label class="form-check-label" for="update_is_admin">Administrador</label>
                    </div>
                    <button type="submit" class="btn">Atualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para preencher o modal de atualização com os dados do funcionário
        var updateModal = document.getElementById('updateFuncionarioModal');
        updateModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var codigo = button.getAttribute('data-codigo');
            var nome = button.getAttribute('data-nome');
            var email = button.getAttribute('data-email');
            var senha = button.getAttribute('data-senha');
            var isAdmin = button.getAttribute('data-admin') == 1;

            var modalId = updateModal.querySelector('#update_id');
            var modalCodigo = updateModal.querySelector('#update_codigo_unico');
            var modalNome = updateModal.querySelector('#update_nome');
            var modalEmail = updateModal.querySelector('#update_email');
            var modalSenha = updateModal.querySelector('#update_senha');
            var modalIsAdmin = updateModal.querySelector('#update_is_admin');

            modalId.value = id;
            modalCodigo.value = codigo;
            modalNome.value = nome;
            modalEmail.value = email;
            modalSenha.value = senha;
            modalIsAdmin.checked = isAdmin;
        });
    </script>
</main>
</body>
</html>

<?php mysqli_close($conn); ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    </main>
        <footer>
            <div class="container">
                <p>&copy; 2025 Snack Fast. Todos os direitos reservados.</p>
            </div>
    </footer>


</body>
</html>


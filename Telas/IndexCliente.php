<?php
include '../DAO/Conexao.php';

use PHP\Modelo\DAO\Conexao;

$conexao = new Conexao();
$conn = $conexao->conectar();

session_start();


if (!isset($_SESSION['cliente_id'])) {

    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilo.css">
    <link rel="stylesheet" href="../css/responsive.css" media="screen">
    <link rel="stylesheet" href="https://use.typekit.net/gvp4xoa.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../js/menu.js" defer></script>
    <script src="../js/btn-menu-mob.js" defer></script>
    <script src="../js/rolagem.js"></script>
    <script src="../js/modal.js" defer></script>
    <script src="../js/icones.js" defer></script>
    <title>Snack Fast</title>
</head>
<body>



    <header id="header">

        <div class="interface">
    
            <section class="logo">
                <img src="../images/logo_snack_fast.png" alt="Logotipo do site">
            </section>
    
            <section class="menu-desktop">
                <nav>
                    <ul>
                        <li><a href="#inicio">INÍCIO</a></li>
                        <li><a href="#cardapio">CARDÁPIO</a></li>
                        <li><a href="SobreCliente.html">SOBRE</a></li>
                    </ul>
                </nav>
            </section>


         <!-- JANELA MODAL DE LOGIN E CARRINHO -->
            <section class="btn-icones">
                <a href="carrinho.php">
                    <i class="fa fa-shopping-cart"></i>
                </a>    
                <a href="#" id="loginStatusBtn">
                    <i class="fa fa-user login-icon" id="login-icon"></i>
                </a>

                <a href="MeusPedidos.php" id="loginStatusBtn">
                    <i class="bi bi-card-checklist"></i>
                </a>

                <!-- Modal -->
<style>
    .modal {
        display: none; /* Oculto por padrão */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro */
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        position: relative; /* Para posicionar o botão de fechar */
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        color: #333;
        cursor: pointer;
        font-weight: bold;
    }

    .close:hover {
        color: #ff0000;
    }

    .logout-btn {
        background-color: #dc3545;
        color: #fff;
        font-weight: bold;
        border: none;
        cursor: pointer;
        padding: 10px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .logout-btn:hover {
        background-color: #c82333;
    }
</style>

<div id="loginModal" class="modal">
    <div class="modal-content">
        <span id="closeLoginModal" class="close">&times;</span>
        <h2>Status de Login</h2>
        <p id="loginMessage"></p>
        <button class="logout-btn" id="logoutBtn">Sair</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Abrir o modal de login
        var loginModal = document.getElementById("loginModal");
        var loginBtn = document.getElementById("loginStatusBtn");
        var closeLoginSpan = document.getElementById("closeLoginModal");

        loginBtn.onclick = function() {
            loginModal.style.display = "flex";
            // Verificar se o usuário está logado
            var isLoggedIn = <?php echo isset($_SESSION['cliente_nome']) ? 'true' : 'false'; ?>;
            var loginMessage = document.getElementById("loginMessage");
            if (isLoggedIn) {
                loginMessage.textContent = "Você está logado como <?php echo $_SESSION['cliente_nome']; ?>.";
            } else {
                loginMessage.textContent = "Você não está logado.";
            }
        }

        closeLoginSpan.onclick = function() {
            loginModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == loginModal) {
                loginModal.style.display = "none";
            }
        }

        // Quando o usuário clicar no botão de logout, faz o logout e redireciona para a página de login
        var logoutBtn = document.getElementById("logoutBtn");
        logoutBtn.onclick = function() {
            window.location.href = 'logout.php';
        }
    });
</script>

            </section>
    


    
            <section class="btn-contato">
                <a href="#contato">
                    <button>CONTATO</button>
                </a>
            </section>
    
            <div class="btn-menu-mob" id="btn-menu-mob">
                <div class="line-menumob-1"></div>
                <div class="line-menumob-2"></div>
            </div>
    
            <section class="menu-mobile" id="menu-mobile">
                <nav>
                    <ul>
                        <li><a href="#inicio">INÍCIO</a></li>
                        <li><a href="#cardapio">CARDÁPIO</a></li>
                        <li><a href="#foodtruck">SOBRE</a></li>
                        <li><a href="#contato">CONTATO</a></li>
                    </ul>
                </nav>
            </section>
    
        </div>
    
    </header>
    

    <section class="hero-site" id="inicio">
        <div class="interface">
            <div class="txt-hero">
                <h1>A rua é nossa cozinha, <span>o sabor é seu!</span></h1>
                <p>Bem-vindo(a) ao Snack Fast. O melhor do lanche sobre rodas de São Bernardo do Campo! Agende a retirada do seu pedido com facilidade.
                    <span class="aviso">Não realizamos entregas!</span></p>
                <a href="#">
                    <button>PEDIR AGORA</button>
                </a>
            </div>
        </div>
    </section>
    <section class="cardapio" id="cardapio">
        <div class="interface">
            <h2 class="categoria-titulo">Hot-Dogs</h2>
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script>
                function addToCart(id) {
                    var form = $('#form-' + id);
                    $.ajax({
                        type: 'POST',
                        url: 'carrinho.php?action=add&id=' + id,
                        data: form.serialize(),
                        success: function(response) {
                            alert('Produto adicionado ao carrinho!');
                        }
                    });
                    return false;
                }
            </script>
<?php
$result = $conn->query("SELECT * FROM produtos WHERE categoria = 'hotdog'");
$counter = 0; // Contador para alternar as classes
while ($row = $result->fetch_assoc()) {
    $class = ($counter % 2 == 0) ? 'left' : 'right'; // Alterna entre 'left' e 'right'
    echo "<article class='itens-container $class'>";
    echo "<div class='img-itens'>";
    echo "<img src='../images/" . $row['imagem'] . "' alt='" . $row['nome'] . "'>";
    echo "</div>";
    echo "<div class='txt-itens'>";
    echo "<h3><span>" . $row['nome'] . "</span></h3>";
    echo "<p>" . $row['descricao'] . "</p>";
    echo "<p class='preco'>R$ " . $row['preco'] . "</p>";
    $adicionais = $conn->query("SELECT a.* FROM adicionais a JOIN produto_adicionais pa ON a.id = pa.adicional_id WHERE pa.produto_id = " . $row['id']);
    echo "<form id='form-" . $row['id'] . "' method='post' onsubmit='return addToCart(" . $row['id'] . ")'>";
    while ($adicional = $adicionais->fetch_assoc()) {
        echo "<div class='checkbox-container'>";
        echo "<input type='checkbox' name='adicional[]' value='" . $adicional['preco'] . "' id='adicional-" . $adicional['id'] . "'>";
        echo "<label for='adicional-" . $adicional['id'] . "'>" . $adicional['nome'] . " (+R$ " . number_format($adicional['preco'], 2, ',', '.') . ")</label>";
        echo "</div>";
    }
    echo "<button type='submit' class='btn-pedir'>Adicionar ao Carrinho</button>";
    echo "</form>";
    echo "</div>";
    echo "</article>";
    $counter++; // Incrementa o contador
}
?>
            <h2 class="categoria-titulo">Hambúrgueres</h2>
<?php
$result = $conn->query("SELECT * FROM produtos WHERE categoria = 'hamburguer'");
$counter = 0; // Contador para alternar as classes
while ($row = $result->fetch_assoc()) {
    $class = ($counter % 2 == 0) ? 'left' : 'right'; // Alterna entre 'left' e 'right'
    echo "<article class='itens-container $class'>";
    echo "<div class='img-itens'>";
    echo "<img src='../images/" . $row['imagem'] . "' alt='" . $row['nome'] . "'>";
    echo "</div>";
    echo "<div class='txt-itens'>";
    echo "<h3><span>" . $row['nome'] . "</span></h3>"; // Removido "Hambúrguer"
    echo "<p>" . $row['descricao'] . "</p>";
    echo "<p class='preco'>R$ " . $row['preco'] . "</p>";
    $adicionais = $conn->query("SELECT a.* FROM adicionais a JOIN produto_adicionais pa ON a.id = pa.adicional_id WHERE pa.produto_id = " . $row['id']);
    echo "<form id='form-" . $row['id'] . "' method='post' onsubmit='return addToCart(" . $row['id'] . ")'>";
    while ($adicional = $adicionais->fetch_assoc()) {
        echo "<div class='checkbox-container'>";
        echo "<input type='checkbox' name='adicional[]' value='" . $adicional['preco'] . "' id='adicional-" . $adicional['id'] . "'>";
        echo "<label for='adicional-" . $adicional['id'] . "'>" . $adicional['nome'] . " (+R$ " . number_format($adicional['preco'], 2, ',', '.') . ")</label>";
        echo "</div>";
    }
    echo "<button type='submit' class='btn-pedir'>Adicionar ao Carrinho</button>";
    echo "</form>";
    echo "</div>";
    echo "</article>";
    $counter++; // Incrementa o contador
}
?>

<h2 class="categoria-titulo">Sanduíches</h2>
<?php
$result = $conn->query("SELECT * FROM produtos WHERE categoria = 'sanduiche'");
$counter = 0; // Reinicia o contador para esta categoria
while ($row = $result->fetch_assoc()) {
    $class = ($counter % 2 == 0) ? 'left' : 'right'; // Alterna entre 'left' e 'right'
    echo "<article class='itens-container $class'>";
    echo "<div class='img-itens'>";
    echo "<img src='../images/" . $row['imagem'] . "' alt='" . $row['nome'] . "'>";
    echo "</div>";
    echo "<div class='txt-itens'>";
    echo "<h3><span>" . $row['nome'] . "</span></h3>"; // Removido "Sanduíche"
    echo "<p>" . $row['descricao'] . "</p>";
    echo "<p class='preco'>R$ " . $row['preco'] . "</p>";
    $adicionais = $conn->query("SELECT a.* FROM adicionais a JOIN produto_adicionais pa ON a.id = pa.adicional_id WHERE pa.produto_id = " . $row['id']);
    echo "<form id='form-" . $row['id'] . "' method='post' onsubmit='return addToCart(" . $row['id'] . ")'>";
    while ($adicional = $adicionais->fetch_assoc()) {
        echo "<div class='checkbox-container'>";
        echo "<input type='checkbox' name='adicional[]' value='" . $adicional['preco'] . "' id='adicional-" . $adicional['id'] . "'>";
        echo "<label for='adicional-" . $adicional['id'] . "'>" . $adicional['nome'] . " (+R$ " . number_format($adicional['preco'], 2, ',', '.') . ")</label>";
        echo "</div>";
    }
    echo "<button type='submit' class='btn-pedir'>Adicionar ao Carrinho</button>";
    echo "</form>";
    echo "</div>";
    echo "</article>";
    $counter++; // Incrementa o contador
}
?>

<h2 class="categoria-titulo">Bebidas</h2>
<?php
$result = $conn->query("SELECT * FROM produtos WHERE categoria = 'bebida'");
$counter = 0; // Reinicia o contador para esta categoria
while ($row = $result->fetch_assoc()) {
    $class = ($counter % 2 == 0) ? 'left' : 'right'; // Alterna entre 'left' e 'right'
    echo "<article class='itens-container $class'>";
    echo "<div class='img-itens'>";
    echo "<img src='../images/" . $row['imagem'] . "' alt='" . $row['nome'] . "'>";
    echo "</div>";
    echo "<div class='txt-itens'>";
    echo "<h3><span>" . $row['nome'] . "</span></h3>"; // Removido "Bebida"
    echo "<p>" . $row['descricao'] . "</p>";
    echo "<p class='preco'>R$ " . $row['preco'] . "</p>";
    $adicionais = $conn->query("SELECT a.* FROM adicionais a JOIN produto_adicionais pa ON a.id = pa.adicional_id WHERE pa.produto_id = " . $row['id']);
    echo "<form id='form-" . $row['id'] . "' method='post' onsubmit='return addToCart(" . $row['id'] . ")'>";
    while ($adicional = $adicionais->fetch_assoc()) {
        echo "<div class='checkbox-container'>";
        echo "<input type='checkbox' name='adicional[]' value='" . $adicional['preco'] . "' id='adicional-" . $adicional['id'] . "'>";
        echo "<label for='adicional-" . $adicional['id'] . "'>" . $adicional['nome'] . " (+R$ " . number_format($adicional['preco'], 2, ',', '.') . ")</label>";
        echo "</div>";
    }
    echo "<button type='submit' class='btn-pedir'>Adicionar ao Carrinho</button>";
    echo "</form>";
    echo "</div>";
    echo "</article>";
    $counter++; // Incrementa o contador
}
?>
        </div>
    </section>
    <section class="foodtruck" id="foodtruck">
        <div class="interface">
            <h3>Quem Somos <span>e o Que Nos Move</span></h3>
            <p>Conheça um pouco mais da nossa história.</p>
            <a href="SobreCliente.html">
                <button>Conheça-nos</button>
            </a>
        </div>
        <div class="overlay"></div>
    </section>
    <section class="contato" id="contato">
        <div class="interface">
            <article class="txt-contato">
                <h3>Entre em
                    <span>contato</span></h3>
            </article>
            <article class="icons-contato">
                <a href="https://wa.me/11916506687">
                    <button><i class="bi bi-whatsapp"></i> <p>Chamar no WhatsApp</p></button>
                </a>
                <button id="openModal">
                    <i class="bi bi-ui-checks"></i>
                    <p>Formulário</p>
                </button>
            </article>
        </div>
    </section>
    <section>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $message = $_POST['message'];

            // Verificar conexão
            if ($conn->connect_error) {
                die("Conexão falhou: " . $conn->connect_error);
            }

            // Inserir dados no banco de dados
            $stmt = $conn->prepare("INSERT INTO contatos (nome, email, mensagem) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $message);
            $stmt->execute();

            // Fechar conexão
            $stmt->close();
            $conn->close();

            // Exibir mensagem de sucesso
            echo "<script>alert('Mensagem enviada com sucesso!');</script>";
        }
        ?>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;700&display=swap');

            .modal {
                display: none; /* Oculto por padrão */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro */
                z-index: 1000;
                justify-content: center;
                align-items: center;
            }

            .modal-content {
                position: relative; /* Para posicionar o botão de fechar */
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                width: 90%;
                max-width: 400px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                font-family: 'Josefin Sans', sans-serif;
            }

            .close {
                position: absolute;
                top: 10px;
                right: 15px;
                font-size: 24px;
                color: #333;
                cursor: pointer;
                font-weight: bold;
            }

            .close:hover {
                color: #ff0000;
            }

            form {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            form input, form textarea, form button {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-family: 'Josefin Sans', sans-serif;
            }

            form button {
                background-color: #DF8B03;
                color: #fff;
                font-weight: bold;
                border: none;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            form button:hover {
                background-color: #c97a02;
            }
        </style>
        <div id="formModal" class="modal">
            <div class="modal-content">
                <!-- Botão de Fechar -->
                <span id="closeFormModal" class="close">&times;</span>
                <h2>Formulário de Contato</h2>
                <form method="post" action="">
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name" required>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="message">Mensagem:</label>
                    <textarea id="message" name="message" required></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Abrir o modal do formulário
                var formModal = document.getElementById("formModal");
                var openModalBtn = document.getElementById("openModal");
                var closeFormSpan = document.getElementById("closeFormModal");

                openModalBtn.onclick = function() {
                    formModal.style.display = "flex";
                }

                closeFormSpan.onclick = function() {
                    formModal.style.display = "none";
                }

                window.onclick = function(event) {
                    if (event.target == formModal) {
                        formModal.style.display = "none";
                    }
                }
            });
        </script>
    </section>
    
    <main>
       <div id="mapa">
       <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2583.492783113447!2d-46.55405939258331!3d-23.68988207250151!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce424af5ceec15%3A0x4eb20edd469e144!2sR.%20Atl%C3%A2ntica%2C%20730%20-%20Jardim%20do%20Mar%2C%20S%C3%A3o%20Bernardo%20do%20Campo%20-%20SP%2C%2009750-480!5e0!3m2!1spt-BR!2sbr!4v1742062198294!5m2!1spt-BR!2sbr" width="1000" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
       </div>

       <div class="info">
            <h1>ENCONTRE-NOS</h1>
            <p>Rua Atlântica, 730. Centro - São Bernado do Campo - SP</p>
            <p>Telefone: (11) 4336 - 7936 ou (11) 4336 - 7936</p>
            <p>snackfast@gmail.com.br</p>
       </div>

    </main>

   
     <footer>
    <div class="interface">
        <section class="top-footer">
            <a href="#"><button><i class="bi bi-instagram"></i></button></a>
            <a href="#"><button><i class="bi bi-facebook"></i></button></a>
            <a href="#"><button><i class="bi bi-tiktok"></i></button></a>
        </section>

        <section class="middle-footer">
            <a href="#">Desenvolvedores</a>
        </section>

        <section class="bottom-footer">
            <p>Snack Fast 2024 &copy; Todos os direitos reservados.</p>
        </section>
    </div>
</footer>


</body>
</html>
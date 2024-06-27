<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Inscrição</title>
    <link rel="stylesheet" href="CSS/formulario_inscricao.css">
</head>
<body>
    <nav class="navbar bg-body-tertiary tab">
        <div class="container-fluid" id="navbar_marca">
            <div id="logo">
                <span class="navbar-brand mb-0" style="color: white;"><img src="img/logo.jpg" alt="BrFideliza"> BrFideliza</span> 
            </div>
            <div id="menu">
                <a href="home.php"><img src="img/home-icon.png" alt="Página Inicial"></a>
                <a href="login.php"><img src="img/user-icon.png" alt="Perfil do Usuário"></a>
                <a href="historico.php"><img src="img/historico-icon.png" alt="Histórico"></a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-left">
                <?php
                require_once 'config.php'; // Inclui o arquivo com detalhes de conexão ao banco de dados
                
                // Verificação da conexão
                if ($conexao->connect_error) {
                    die("Falha na conexão: " . $conexao->connect_error);
                }
                
                // Query para recuperar os detalhes do evento (exemplo)
                $idEvento = 1; // ID do evento específico
                $sql = "SELECT eveTitulo, eveDescricao, eveLocal, eveData, eveImgCapa FROM evento WHERE idEvento = $idEvento";
                $result = $conexao->query($sql);
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    ?>
                    <div class="event-details">
                        <img src="IMG/evento_imagem.jpeg" alt="">
                        <h2>Detalhes do Evento</h2>
                        <p>
                            <strong>Título:</strong> <?php echo $row['eveTitulo']; ?><br>
                            <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($row['eveData'])); ?><br>
                            <strong>Local:</strong> <?php echo $row['eveLocal']; ?>
                        </p>
                        <p><?php echo $row['eveDescricao']; ?></p>
                    </div>
                    <?php
                } else {
                    echo "Nenhum evento encontrado.";
                }
                
                // Fechar conexão
                $conexao->close();
                ?>
            </div>
            <div class="col-right">
                <div class="registration-form">
                    <h2>Formulário de Inscrição</h2>
                    <form action="processo_formulario.php" method="post">
                        <label for="nome">Nome Completo:</label><br>
                        <input type="text" id="nome" name="nome" required><br>
                        <label for="telefone">Telefone:</label><br>
                        <input type="text" id="telefone" name="telefone" required><br>
                        <label for="email">E-mail:</label><br>
                        <input type="email" id="email" name="email" required><br><br>
                        <button type="submit">Enviar Inscrição</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer>
       <p class="rodape">© 2024 BrFideliza. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

<?php
require_once '../config.php'; // Inclui o arquivo com detalhes de conexão ao banco de dados

// Verifica se foi fornecido um ID válido na URL

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idInscricao = $_GET['id'];

    // Query para recuperar os detalhes da inscrição pendente
    $sql = "SELECT i.idInscricao, i.insDataRegistro, e.eveTitulo, p.nomeCompleto, p.email
            FROM inscricao i
            INNER JOIN evento e ON i.evento_idEvento = e.idEvento
            INNER JOIN pedido pd ON i.pedido_idPedido = pd.idPedido
            INNER JOIN pessoa p ON pd.Pessoa_idPessoa = p.idPessoa
            WHERE i.idInscricao = $idInscricao AND i.ins_confirmada = 0";

    $result = $conexao->query($sql);

    if ($result->num_rows == 1) {
        $inscricao = $result->fetch_assoc();
    } else {
        // Caso não encontre a inscrição pendente, redireciona de volta à página anterior
        header("Location: revisao_inscricao.php");
        exit();
    }
} else {
    // Caso o ID não seja fornecido corretamente na URL, redireciona de volta à página anterior
    header("Location: revisao_inscricao.php");
    exit();
}

// Função para atualizar o status da inscrição para "confirmada"
function confirmarInscricao($idInscricao, $conexao) {
    $sqlUpdate = "UPDATE inscricao SET ins_confirmada = 1 WHERE idInscricao = $idInscricao";
    if ($conexao->query($sqlUpdate) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Verifica se o formulário foi submetido para confirmar a inscrição
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar'])) {
    $idInscricao = $_POST['idInscricao'];
    if (confirmarInscricao($idInscricao, $conexao)) {
        // Redireciona para a página de revisão de inscrições após a confirmação
        header("Location: revisao_inscricoes.php");
        exit();
    } else {
        // Tratamento de erro, se necessário
        $erroMsg = "Erro ao confirmar a inscrição. Por favor, tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Inscrição</title>
    <link rel="stylesheet" href="../CSS/admin.css"> <!-- Estilo CSS personalizado -->
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar bg-body-tertiary tab">
        <div class="container-fluid" id="navbar_marca">
            <div id="logo">
                <span class="navbar-brand mb-0" style="color: white;"><img src="../img/logo.jpg" alt="BrFideliza">BrFideliza</span>
            </div>
            <div id="menu">
                <a href="../home.php"><img src="../img/home-icon.png" alt="Página Inicial"></a>
                <a href="../login.php"><img src="../img/user-icon.png" alt="Perfil do Usuário"></a>
                <a href="../historico.php"><img src="../img/historico-icon.png" alt="Histórico"></a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <div class="container">
        <h2>Confirmar Inscrição Pendente</h2>

        <div class="detalhes-inscricao">
            <p><strong>ID da Inscrição:</strong> <?php echo $inscricao['idInscricao']; ?></p>
            <p><strong>Data de Inscrição:</strong> <?php echo date('d/m/Y H:i:s', strtotime($inscricao['insDataRegistro'])); ?></p>
            <p><strong>Evento:</strong> <?php echo $inscricao['eveTitulo']; ?></p>
            <p><strong>Nome:</strong> <?php echo $inscricao['nomeCompleto']; ?></p>
            <p><strong>E-mail:</strong> <?php echo $inscricao['email']; ?></p>
        </div>

        <form method="post">
            <input type="hidden" name="idInscricao" value="<?php echo $inscricao['idInscricao']; ?>">
            <button type="submit" name="confirmar">Confirmar Inscrição</button>
        </form>

        <?php if (isset($erroMsg)) : ?>
            <p class="erro"><?php echo $erroMsg; ?></p>
        <?php endif; ?>
    </div>

    <!-- Rodapé -->
    <footer>
       <p class="rodape">© 2024 BrFideliza. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

<?php
// Fecha a conexão com o banco de dados
$conexao->close();
?>

<!-- Ajustar: Faca um exemplo de pendencia e a confimarção de escriçoes, para vê se funciona -->

<?php
require_once '../config.php'; // Inclui o arquivo com detalhes de conexão ao banco de dados

// Query para recuperar as inscrições pendentes
$sql = "SELECT i.idInscricao, i.insDataRegistro, e.eveTitulo, p.nomeCompleto, p.email
        FROM inscricao i
        INNER JOIN evento e ON i.evento_idEvento = e.idEvento
        INNER JOIN pedido pd ON i.pedido_idPedido = pd.idPedido
        INNER JOIN pessoa p ON pd.Pessoa_idPessoa = p.idPessoa
        WHERE i.ins_confirmada = 0
        ORDER BY i.insDataRegistro DESC";

$result = $conexao->query($sql);

// Array para armazenar as inscrições do banco de dados
$inscricoes = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $inscricoes[] = $row; // Adiciona cada inscrição ao array $inscricoes
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisão de Inscrições</title>
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
        <h2>Revisão de Inscrições Pendentes</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data de Inscrição</th>
                    <th>Evento</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Exibição das inscrições do array $inscricoes
                foreach ($inscricoes as $inscricao) {
                    echo "<tr>";
                    echo "<td>" . $inscricao["idInscricao"] . "</td>";
                    echo "<td>" . date('d/m/Y H:i:s', strtotime($inscricao["insDataRegistro"])) . "</td>";
                    echo "<td>" . $inscricao["eveTitulo"] . "</td>";
                    echo "<td>" . $inscricao["nomeCompleto"] . "</td>";
                    echo "<td>" . $inscricao["email"] . "</td>";
                    echo "<td><a href='confirmar_inscricao.php?id=" . $inscricao["idInscricao"] . "'>Confirmar</a></td>"; // Link para confirmar inscrição
                    echo "</tr>";
                }

                if (empty($inscricoes)) {
                    echo "<tr><td colspan='6'>Nenhuma inscrição pendente encontrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
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

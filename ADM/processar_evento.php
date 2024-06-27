<?php
// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configurações do banco de dados
    require_once '../config.php';

    // Verifica qual ação está sendo executada
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Ação: Adicionar Evento
        if ($action == 'add_event') {
            // Verifica se todos os campos foram recebidos
            $campos = ['titulo', 'descricao', 'local', 'data', 'inicio_inscricao', 'fim_inscricao', 'vl_ingresso', 'vl_com_desconto', 'empresa_id'];
            foreach ($campos as $campo) {
                if (!isset($_POST[$campo])) {
                    die("Campo '$campo' não foi especificado.");
                }
            }

                // Prepara os dados para inserção
                $titulo = sanitize($conexao, $_POST['titulo']);
                $descricao = sanitize($conexao, $_POST['descricao']);
                $local = sanitize($conexao, $_POST['local']);
                $data = formatDateTime($_POST['data']);
                $inicio_inscricao = formatDateTime($_POST['inicio_inscricao']);
                $fim_inscricao = formatDateTime($_POST['fim_inscricao']);
                $vl_ingresso = (float)$_POST['vl_ingresso'];
                $vl_com_desconto = (float)$_POST['vl_com_desconto'];
                $empresa_id = (int)$_POST['empresa_id'];

                // Imprimir variáveis com var_dump()
                var_dump($data, $inicio_inscricao, $fim_inscricao);

                // Redireciona para a página de lista de eventos
                header('Location: listar_eventos.php');

                // Termina a execução após var_dump()
                exit();

                // SQL statement para inserir evento na tabela 'evento'
                $sql = "INSERT INTO evento (eveTitulo, eveTituloHash, eveDescricao, eveLocal, eveData, eveDtIniInscricao, eveDtFimInscricao, eveVlIngresso, eveVlComDesconto, empresa_idEmpresa, eveAtivo)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

                // Prepare a declaração SQL
                $stmt = $conexao->prepare($sql);
                if ($stmt === false) {
                    die('Erro na preparação da consulta: ' . $conexao->error);
                }

                // Calcular hash do título
                $tituloHash = md5($titulo);

                // Bind dos parâmetros
                $stmt->bind_param("ssssssddii", $titulo, $tituloHash, $descricao, $local, $data, $inicio_inscricao, $fim_inscricao, $vl_ingresso, $vl_com_desconto, $empresa_id);

                // Executa a consulta SQL
                if ($stmt->execute() === TRUE) {
                    echo "Evento adicionado com sucesso!";
                } else {
                    echo "Erro ao adicionar evento: " . $stmt->error;
                }

                // Fecha o statement
                $stmt->close();
            $stmt->close();
        }

        // Ação: Editar Evento
        elseif ($action == 'edit_event') {
            // Verifica se todos os campos foram recebidos
            $campos = ['idEvento', 'titulo', 'descricao', 'local', 'data', 'inicio_inscricao', 'fim_inscricao', 'vl_ingresso', 'vl_com_desconto'];
            foreach ($campos as $campo) {
                if (!isset($_POST[$campo])) {
                    die("Campo '$campo' não foi especificado.");
                }
            }

            // Prepara os dados para edição
            $idEvento = (int)$_POST['idEvento'];
            $titulo = sanitize($conexao, $_POST['titulo']);
            $descricao = sanitize($conexao, $_POST['descricao']);
            $local = sanitize($conexao, $_POST['local']);
            $data = formatDateTime($_POST['data']);
            $inicio_inscricao = formatDateTime($_POST['inicio_inscricao']);
            $fim_inscricao = formatDateTime($_POST['fim_inscricao']);
            $vl_ingresso = (float)$_POST['vl_ingresso'];
            $vl_com_desconto = (float)$_POST['vl_com_desconto'];

            // Imprimir variáveis com var_dump()
            var_dump($data, $inicio_inscricao, $fim_inscricao);

            // Redireciona para a página de lista de eventos
            header('Location: listar_eventos.php');

            // Termina a execução após var_dump()
            exit();
            // Calcular hash do título
            $tituloHash = md5($titulo);

            // SQL statement para atualizar evento na tabela 'evento'
            $sql = "UPDATE evento 
                    SET eveTitulo = ?, eveTituloHash = ?, eveDescricao = ?, eveLocal = ?, eveData = ?, eveDtIniInscricao = ?, eveDtFimInscricao = ?, eveVlIngresso = ?, eveVlComDesconto = ?
                    WHERE idEvento = ?";

            // Prepare a declaração SQL
            $stmt = $conexao->prepare($sql);
            if ($stmt === false) {
                die('Erro na preparação da consulta: ' . $conexao->error);
            }

            // Bind dos parâmetros
            $stmt->bind_param("ssssssddii", $titulo, $tituloHash, $descricao, $local, $data, $inicio_inscricao, $fim_inscricao, $vl_ingresso, $vl_com_desconto, $idEvento);

            // Executa a consulta SQL
            if ($stmt->execute() === TRUE) {
                echo "Evento editado com sucesso!";
            } else {
                echo "Erro ao editar evento: " . $stmt->error;
            }

            // Fecha o statement
            $stmt->close();
        }

        // Ação: Excluir Evento
        elseif ($action == 'delete_event') {
            // Verifica se o campo 'idEvento' foi recebido
            if (!isset($_POST['idEvento'])) {
                die("ID do evento não foi especificado.");
            }

            // Prepara o ID do evento para exclusão
            $idEvento = (int)$_POST['idEvento'];

            // SQL statement para excluir evento da tabela 'evento'
            $sql = "DELETE FROM evento WHERE idEvento = ?";

            // Prepare a declaração SQL
            $stmt = $conexao->prepare($sql);
            if ($stmt === false) {
                die('Erro na preparação da consulta: ' . $conexao->error);
            }

            // Bind dos parâmetros
            $stmt->bind_param("i", $idEvento);

            // Executa a consulta SQL
            if ($stmt->execute() === TRUE) {
                echo "Evento excluído com sucesso!";
            } else {
                echo "Erro ao excluir evento: " . $stmt->error;
            }
            // Redireciona para a página de lista de eventos
            header('Location: listar_eventos.php');

            // Fecha o statement
            $stmt->close();
        }
    }

    // Fecha a conexão com o banco de dados
    $conexao->close();
} else {
    die("Acesso não permitido.");
}

// Função para formatar data e hora
function formatDateTime($datetime) {
    $date = new DateTime($datetime);
    return $date->format('Y-m-d H:i:s');
}

// Função para sanitizar dados
function sanitize($conexao, $input) {
    return $conexao->real_escape_string($input);
}
?>

<?php
// Exemplo de dados de eventos
$eventos = array(
    array("idInscricao" => 32, "insDataRegistro" => "2024-10-15 09:00:00", "eveTitulo" => "Show TKJ", "nomeCompleto" => "Fulano de Tal", "telefone" => "+551234567890", "email" => "fulano@example.com", "pag_status" => "inscrito"),
    array("idInscricao" => 33, "insDataRegistro" => "2024-10-16 10:30:00", "eveTitulo" => "show Test", "nomeCompleto" => "izabelle", "telefone" => "+5521987654321", "email" => "izabelle.ferreira2010@bol.com.br", "pag_status" => "pago"),
    array("idInscricao" => 34, "insDataRegistro" => "2024-05-25 14:45:00", "eveTitulo" => "Hora do show", "nomeCompleto" => "Maria", "telefone" => "+5531976543210", "email" => "maria@gmail.com", "pag_status" => "presente")
);

// Inclui o arquivo de configuração
require_once '../config.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

// Inicialização da variável de filtro
$statusFilter = '';

// Verifica se o filtro por tipo e status foi enviado via GET
if (isset($_GET['type']) && isset($_GET['status'])) {
    $type = $_GET['type'];
    $statusFilter = $_GET['status'];

    // Verifica se o valor do filtro é válido (inscrito, pago, presente)
    if (!in_array($statusFilter, ['inscrito', 'pago', 'presente'])) {
        $statusFilter = ''; // Caso não seja válido, limpa o filtro
    }

    // Filtra os dados conforme o status, se aplicável
    $dados = array_filter($eventos, function($evento) use ($statusFilter) {
        return empty($statusFilter) || $evento['pag_status'] == $statusFilter;
    });

    // Definir o tipo de conteúdo conforme o tipo de exportação
    switch ($type) {
        case 'pdf':
            // Lógica para gerar PDF usando TCPDF
            require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

            // Instanciar a classe TCPDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Título do documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Seu Nome');
            $pdf->SetTitle('Exportação de Inscrições');
            $pdf->SetSubject('Inscrições em Eventos');
            $pdf->SetKeywords('Inscrições, Eventos, PDF');

            // Adicionar uma página
            $pdf->AddPage();

            // Cabeçalho do PDF
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 10, 'Lista de Inscrições em Eventos', 0, 1, 'C');

            // Dados das inscrições
            foreach ($dados as $row) {
                $pdf->SetFont('times', '', 10);
                $pdf->Cell(30, 10, 'ID:', 1, 0, 'L');
                $pdf->Cell(50, 10, $row["idInscricao"], 1, 0, 'L');
                $pdf->Cell(40, 10, 'Data de Inscrição:', 1, 0, 'L');
                $pdf->Cell(70, 10, date('d/m/Y H:i:s', strtotime($row["insDataRegistro"])), 1, 1, 'L');
                $pdf->Cell(30, 10, 'Evento:', 1, 0, 'L');
                $pdf->Cell(160, 10, $row["eveTitulo"], 1, 1, 'L');
                $pdf->Cell(30, 10, 'Nome:', 1, 0, 'L');
                $pdf->Cell(160, 10, $row["nomeCompleto"], 1, 1, 'L');
                $pdf->Cell(30, 10, 'E-mail:', 1, 0, 'L');
                $pdf->Cell(160, 10, $row["email"], 1, 1, 'L');
                $pdf->Cell(30, 10, 'Status Pagamento:', 1, 0, 'L');
                $pdf->Cell(160, 10, ucfirst($row["pag_status"]), 1, 1, 'L');
                $pdf->Ln(5);
            }

            // Saída do PDF
            $pdf->Output('inscricoes.pdf', 'D');
            exit;
            break;
        case 'csv':
            // Cabeçalho para download do arquivo CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=inscricoes.csv');
            // Cria um arquivo de saída temporário
            $output = fopen('php://output', 'w');
            // Cabeçalho do CSV
            fputcsv($output, array('ID', 'Data de Inscrição', 'Evento', 'Nome', 'E-mail', 'Status Pagamento'));
            // Dados das inscrições
            foreach ($dados as $row) {
                fputcsv($output, array(
                    $row["idInscricao"],
                    date('d/m/Y H:i:s', strtotime($row["insDataRegistro"])),
                    $row["eveTitulo"],
                    $row["nomeCompleto"],
                    $row["email"],
                    ucfirst($row["pag_status"])
                ));
            }
            // Fechar arquivo de saída
            fclose($output);
            exit;
            break;
        case 'excel':
            // Cabeçalho para download do arquivo Excel
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=inscricoes.xls');
            // Saída do Excel (formato HTML)
            echo "<table border='1'>
                    <tr><th>ID</th><th>Data de Inscrição</th><th>Evento</th><th>Nome</th><th>E-mail</th><th>Status Pagamento</th></tr>";
            foreach ($dados as $row) {
                echo "<tr>
                        <td>".$row["idInscricao"]."</td>
                        <td>".date('d/m/Y H:i:s', strtotime($row["insDataRegistro"]))."</td>
                        <td>".$row["eveTitulo"]."</td>
                        <td>".$row["nomeCompleto"]."</td>
                        <td>".$row["email"]."</td>
                        <td>".ucfirst($row["pag_status"])."</td>
                      </tr>";
            }
            echo "</table>";
            exit;
            break;
        default:
            // Tipo de exportação inválido
            echo "Tipo de exportação inválido.";
            exit;
            break;
    }
} else {
    // Parâmetros type ou status não foram fornecidos
    echo "Parâmetros type ou status não foram fornecidos.";
    exit;
}

// Fechar conexão com o banco de dados
$conexao->close();
?>

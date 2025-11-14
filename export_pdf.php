<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();

// load library FPDF
require_once __DIR__ . '/../inc/fpdf.php';

// ambil parameter optional saver_id
$saver_id = isset($_GET['saver_id']) && $_GET['saver_id'] !== '' ? (int)$_GET['saver_id'] : null;

// ambil data transaksi
$transactions = get_transactions($saver_id);
$title = $saver_id ? 'Laporan Transaksi Penabung ID ' . $saver_id : 'Laporan Transaksi Semua Penabung';

// buat PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10, $title,0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,8,'Di-export: ' . date('Y-m-d H:i:s'),0,1,'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(10,8,'#',1,0,'C',true);
$pdf->Cell(40,8,'Nama',1,0,'C',true);
$pdf->Cell(30,8,'Jumlah',1,0,'C',true);
$pdf->Cell(20,8,'Jenis',1,0,'C',true);
$pdf->Cell(35,8,'Waktu',1,0,'C',true);
$pdf->Cell(55,8,'Catatan',1,1,'C',true);

// Isi tabel
$pdf->SetFont('Arial','',9);
$i=1;
if ($transactions) {
    foreach ($transactions as $t) {
        $pdf->Cell(10,7,$i++,1,0,'C');
        $pdf->Cell(40,7,utf8_decode($t['nama']),1);
        $pdf->Cell(30,7,'Rp '.number_format($t['amount'],0,',','.'),1);
        $pdf->Cell(20,7,$t['jenis'],1,0,'C');
        $pdf->Cell(35,7,$t['created_at'],1);
        $pdf->Cell(55,7,utf8_decode($t['note']),1,1);
    }
} else {
    $pdf->Cell(190,7,'Tidak ada transaksi',1,1,'C');
}

// output PDF
$filename = 'laporan_transaksi_' . ($saver_id ? 'saver_'.$saver_id . '_' : '') . date('Ymd_His') . '.pdf';
$pdf->Output('I', $filename);
exit;
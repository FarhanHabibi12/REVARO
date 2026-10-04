<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KebijakanKomisi;
use App\Models\Order;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TokoFinanceController extends Controller
{
    /**
     * Get financial statistics and transaction ledger for authenticated Toko.
     */
    public function getFinanceSummary(Request $request)
    {
        $user = $request->user();

        if (!$user->isToko() || !$user->toko) {
            return response()->json(['message' => 'Hanya mitra toko yang dapat mengakses menu keuangan.'], 403);
        }

        $toko = $user->toko;

        // Fetch platform commission policy
        $kebijakan = KebijakanKomisi::first();
        $persenFee = $kebijakan ? (float)$kebijakan->persen_fee_sparepart : 5.0;

        // Fetch all sparepart orders associated with this shop
        $ledger = DB::table('order_sparepart')
            ->join('sparepart', 'order_sparepart.sparepart_id', '=', 'sparepart.id')
            ->join('orders', 'order_sparepart.order_id', '=', 'orders.id')
            ->leftJoin('teknisi', 'orders.teknisi_id', '=', 'teknisi.id')
            ->leftJoin('pelanggan', 'orders.pelanggan_id', '=', 'pelanggan.id')
            ->leftJoin('transaksi', 'orders.id', '=', 'transaksi.order_id')
            ->where('sparepart.toko_id', $toko->id)
            ->select(
                'order_sparepart.id as item_id',
                'order_sparepart.order_id',
                'order_sparepart.qty',
                'order_sparepart.harga',
                'order_sparepart.subtotal',
                'order_sparepart.status as status_part',
                'order_sparepart.created_at as tanggal_pesan',
                'sparepart.nama as nama_sparepart',
                'sparepart.merk as merk_sparepart',
                'orders.kode_order',
                'orders.status as status_order',
                'teknisi.nama as nama_teknisi',
                'pelanggan.nama as nama_pelanggan',
                'transaksi.status_pembayaran',
                'transaksi.status_escrow',
                'transaksi.paid_at'
            )
            ->orderBy('order_sparepart.created_at', 'desc')
            ->get();

        // Calculate aggregates
        $totalOmsetKotor = 0;
        $totalKomisiPlatform = 0;
        $totalPendapatanBersih = 0;
        $totalItemTerjual = 0;
        $transaksiSuksesCount = 0;

        $formattedLedger = $ledger->map(function ($item) use ($persenFee, &$totalOmsetKotor, &$totalKomisiPlatform, &$totalPendapatanBersih, &$totalItemTerjual, &$transaksiSuksesCount) {
            $subtotal = (float)$item->subtotal;
            $feePlatform = round($subtotal * ($persenFee / 100), 2);
            $pendapatanBersih = $subtotal - $feePlatform;

            $isSuccess = in_array($item->status_part, ['CONFIRMED', 'DELIVERED', 'RECEIVED']) 
                         || in_array($item->status_order, ['IN_PROGRESS', 'AWAITING_PAYMENT', 'COMPLETED', 'RESOLVED'])
                         || $item->status_pembayaran === 'PAID';

            if ($isSuccess) {
                $totalOmsetKotor += $subtotal;
                $totalKomisiPlatform += $feePlatform;
                $totalPendapatanBersih += $pendapatanBersih;
                $totalItemTerjual += (int)$item->qty;
                $transaksiSuksesCount++;
            }

            return [
                'item_id' => $item->item_id,
                'order_id' => $item->order_id,
                'kode_order' => $item->kode_order,
                'tanggal' => Carbon::parse($item->tanggal_pesan)->format('d M Y H:i'),
                'nama_sparepart' => $item->nama_sparepart,
                'merk' => $item->merk_sparepart,
                'qty' => (int)$item->qty,
                'harga' => (float)$item->harga,
                'subtotal' => $subtotal,
                'fee_komisi' => $feePlatform,
                'pendapatan_bersih' => $pendapatanBersih,
                'status_part' => $item->status_part,
                'status_order' => $item->status_order,
                'teknisi_nama' => $item->nama_teknisi ?? 'Teknisi Umum',
                'pelanggan_nama' => $item->nama_pelanggan ?? 'Pelanggan',
                'status_pembayaran' => $item->status_pembayaran ?? 'UNPAID',
                'is_success' => $isSuccess,
            ];
        });

        $walletSaldo = $toko->user ? $toko->user->getUnifiedSaldo() : (float)$toko->saldo;

        return response()->json([
            'toko' => [
                'id' => $toko->id,
                'nama' => $toko->nama,
                'alamat' => $toko->alamat,
                'telepon' => $toko->telepon,
                'saldo' => $walletSaldo,
            ],
            'summary' => [
                'total_omset' => $totalOmsetKotor,
                'total_fee_platform' => $totalKomisiPlatform,
                'pendapatan_bersih' => $totalPendapatanBersih,
                'saldo_saat_ini' => $walletSaldo,
                'total_transaksi' => $transaksiSuksesCount,
                'total_item_terjual' => $totalItemTerjual,
                'persen_komisi' => $persenFee,
            ],
            'ledger' => $formattedLedger,
        ]);
    }

    /**
     * Export Toko financial ledger as a genuine Excel spreadsheet (.xlsx, NOT CSV).
     */
    public function exportExcel(Request $request)
    {
        $user = $request->user();

        // Handle token query fallback for direct browser download links if needed
        if (!$user && $request->has('token')) {
            $personalToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->query('token'));
            if ($personalToken) {
                $user = $personalToken->tokenable;
            }
        }

        if (!$user || !$user->isToko() || !$user->toko) {
            return response()->json(['message' => 'Unauthorized / Forbidden.'], 403);
        }

        $toko = $user->toko;

        // Fetch platform commission policy
        $kebijakan = KebijakanKomisi::first();
        $persenFee = $kebijakan ? (float)$kebijakan->persen_fee_sparepart : 5.0;

        // Fetch transaction records
        $ledger = DB::table('order_sparepart')
            ->join('sparepart', 'order_sparepart.sparepart_id', '=', 'sparepart.id')
            ->join('orders', 'order_sparepart.order_id', '=', 'orders.id')
            ->leftJoin('teknisi', 'orders.teknisi_id', '=', 'teknisi.id')
            ->leftJoin('pelanggan', 'orders.pelanggan_id', '=', 'pelanggan.id')
            ->leftJoin('transaksi', 'orders.id', '=', 'transaksi.order_id')
            ->where('sparepart.toko_id', $toko->id)
            ->select(
                'order_sparepart.id as item_id',
                'order_sparepart.qty',
                'order_sparepart.harga',
                'order_sparepart.subtotal',
                'order_sparepart.status as status_part',
                'order_sparepart.created_at as tanggal_pesan',
                'sparepart.nama as nama_sparepart',
                'sparepart.merk as merk_sparepart',
                'orders.kode_order',
                'orders.status as status_order',
                'teknisi.nama as nama_teknisi',
                'pelanggan.nama as nama_pelanggan',
                'transaksi.status_pembayaran'
            )
            ->orderBy('order_sparepart.created_at', 'desc')
            ->get();

        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan Toko');

        // Styles
        $brandBlue = '0B4A8F';
        $brandOrange = 'FF7F00';

        // Title Block
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'REVARO — LAPORAN KEUANGAN & PEMBUKUAN PENJUALAN TOKO MITRA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($brandBlue);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension('1')->setRowHeight(35);

        // Metadata Info
        $sheet->setCellValue('A3', 'Nama Toko:');
        $sheet->setCellValue('B3', $toko->nama);
        $sheet->setCellValue('A4', 'Alamat:');
        $sheet->setCellValue('B4', $toko->alamat ?: '—');
        $sheet->setCellValue('A5', 'Telepon / WA:');
        $sheet->setCellValue('B5', $toko->telepon ?: '—');
        $sheet->setCellValue('A6', 'Saldo Dompet:');
        $sheet->setCellValue('B6', 'Rp ' . number_format($toko->saldo, 0, ',', '.'));

        $sheet->setCellValue('H3', 'Tanggal Cetak:');
        $sheet->setCellValue('I3', Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->setCellValue('H4', 'Biaya Layanan Platform:');
        $sheet->setCellValue('I4', $persenFee . '%');
        $sheet->setCellValue('H5', 'Status Akun:');
        $sheet->setCellValue('I5', $toko->status ?? 'ACTIVE');

        $sheet->getStyle('A3:A6')->getFont()->setBold(true);
        $sheet->getStyle('H3:H5')->getFont()->setBold(true);

        // Table Header
        $startRow = 8;
        $headers = [
            'A' => 'No',
            'B' => 'Kode Order',
            'C' => 'Waktu Transaksi',
            'D' => 'Nama Suku Cadang',
            'E' => 'Merk',
            'F' => 'Qty',
            'G' => 'Harga Satuan (Rp)',
            'H' => 'Total Omset (Rp)',
            'I' => 'Komisi Platform (Rp)',
            'J' => 'Pendapatan Bersih (Rp)',
            'K' => 'Teknisi / Pelanggan',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . $startRow, $title);
        }

        $sheet->getStyle("A{$startRow}:K{$startRow}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$startRow}:K{$startRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($brandBlue);
        $sheet->getStyle("A{$startRow}:K{$startRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($startRow)->setRowHeight(25);

        // Populate Rows
        $currentRow = $startRow + 1;
        $num = 1;
        $totalOmset = 0;
        $totalKomisi = 0;
        $totalBersih = 0;

        foreach ($ledger as $item) {
            $subtotal = (float)$item->subtotal;
            $komisi = round($subtotal * ($persenFee / 100), 2);
            $net = $subtotal - $komisi;

            $totalOmset += $subtotal;
            $totalKomisi += $komisi;
            $totalBersih += $net;

            $sheet->setCellValue('A' . $currentRow, $num++);
            $sheet->setCellValue('B' . $currentRow, $item->kode_order);
            $sheet->setCellValue('C' . $currentRow, Carbon::parse($item->tanggal_pesan)->format('d/m/Y H:i'));
            $sheet->setCellValue('D' . $currentRow, $item->nama_sparepart);
            $sheet->setCellValue('E' . $currentRow, $item->merk_sparepart ?: '-');
            $sheet->setCellValue('F' . $currentRow, (int)$item->qty);
            $sheet->setCellValue('G' . $currentRow, (float)$item->harga);
            $sheet->setCellValue('H' . $currentRow, $subtotal);
            $sheet->setCellValue('I' . $currentRow, $komisi);
            $sheet->setCellValue('J' . $currentRow, $net);
            $sheet->setCellValue('K' . $currentRow, ($item->nama_teknisi ?: 'Teknisi') . ' / ' . ($item->nama_pelanggan ?: 'Pelanggan'));

            // Number formatting
            $sheet->getStyle('G' . $currentRow . ':J' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternating row background
            if ($num % 2 == 0) {
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $currentRow++;
        }

        // Summary Row at Bottom
        $sheet->mergeCells("A{$currentRow}:G{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'TOTAL KESELURUHAN (IDR)');
        $sheet->setCellValue("H{$currentRow}", "=SUM(H" . ($startRow + 1) . ":H" . ($currentRow - 1) . ")");
        $sheet->setCellValue("I{$currentRow}", "=SUM(I" . ($startRow + 1) . ":I" . ($currentRow - 1) . ")");
        $sheet->setCellValue("J{$currentRow}", "=SUM(J" . ($startRow + 1) . ":J" . ($currentRow - 1) . ")");
        $sheet->setCellValue("K{$currentRow}", count($ledger) . ' Item Transaksi');

        $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$currentRow}:J{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
        $sheet->getRowDimension($currentRow)->setRowHeight(24);

        // Border styling for the table
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle("A{$startRow}:K{$currentRow}")->applyFromArray($styleBorder);

        // Auto-fit column widths
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Export as Binary Excel (.xlsx) Stream
        $filename = 'Laporan_Keuangan_Toko_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $toko->nama) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}

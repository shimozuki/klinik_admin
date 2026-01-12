<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BookingSummaryWidget;
use App\Filament\Widgets\DashboardStatsWidget;
use App\Filament\Widgets\LayananPopulerChartWidget;
use App\Filament\Widgets\PendapatanChartWidget;
use App\Filament\Widgets\ReservasiChartWidget;
use App\Filament\Widgets\ReservasiTerbaruWidget;
use App\Models\RekamMedis;
use App\Models\Reservasi;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use UnitEnum;
use Illuminate\Support\Collection;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanAnalitik extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan & Analitik';
    protected static string | UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.laporan-analitik';

    /**
     * HEADER WIDGET (STATISTIK)
     * Widget harus QUERY SENDIRI (tidak boleh panggil Page)
     */
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
            BookingSummaryWidget::class,
            ReservasiChartWidget::class,
            PendapatanChartWidget::class,
            LayananPopulerChartWidget::class,
            ReservasiTerbaruWidget::class,
        ];
    }

    /**
     * TABLE (FILAMENT v4)
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading('Statistik Bulanan')
            ->description('Performa 6 bulan terakhir')
            ->records(fn(): array => $this->getMonthlyStats()->all())
            ->columns([
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->weight('bold'),

                BadgeColumn::make('total')
                    ->label('Total Booking')
                    ->color('info'),

                BadgeColumn::make('selesai')
                    ->label('Selesai')
                    ->color('success'),

                BadgeColumn::make('batal')
                    ->label('Batal')
                    ->color('danger'),

                TextColumn::make('pendapatan')
                    ->label('Pendapatan')
                    ->money('IDR', locale: 'id'),
            ])
            ->actions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->modalHeading('Export Laporan')
                    ->modalWidth('lg')
                    ->form([
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Laporan')
                            ->options([
                                'booking' => 'Laporan Booking',
                                'pendapatan' => 'Laporan Pendapatan',
                            ])
                            ->required(),

                        Forms\Components\Radio::make('format')
                            ->label('Format File')
                            ->options([
                                'pdf' => 'PDF',
                                'excel' => 'Excel',
                            ])
                            ->default('pdf')
                            ->required(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->native(false),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->action(function (array $data) {

                        // ================= PDF =================
                        if ($data['format'] === 'pdf') {

                            if ($data['jenis'] === 'booking') {

                                $query = Reservasi::with(['pasien.user', 'dokter']);

                                if (!empty($data['start_date'])) {
                                    $query->whereDate('tanggal_reservasi', '>=', $data['start_date']);
                                }

                                if (!empty($data['end_date'])) {
                                    $query->whereDate('tanggal_reservasi', '<=', $data['end_date']);
                                }

                                $dataBooking = $query
                                    ->orderBy('tanggal_reservasi')
                                    ->get();

                                $pdf = Pdf::loadView('pdf.laporan-booking', [
                                    'data' => $dataBooking,
                                    'startDate' => $data['start_date'] ?? null,
                                    'endDate' => $data['end_date'] ?? null,
                                ])->setPaper('A4', 'portrait');

                                return response()->streamDownload(
                                    fn() => print($pdf->output()),
                                    'laporan-booking.pdf'
                                );
                            }

                            if ($data['jenis'] === 'pendapatan') {

                                $query = RekamMedis::with([
                                    'pasien.user',
                                    'dokter',
                                    'reservasi',
                                ]);

                                if (!empty($data['start_date'])) {
                                    $query->whereDate('tanggal_pemeriksaan', '>=', $data['start_date']);
                                }

                                if (!empty($data['end_date'])) {
                                    $query->whereDate('tanggal_pemeriksaan', '<=', $data['end_date']);
                                }

                                $dataPendapatan = $query
                                    ->orderBy('tanggal_pemeriksaan')
                                    ->get();

                                $pdf = Pdf::loadView('pdf.laporan-pendapatan', [
                                    'data' => $dataPendapatan,
                                    'startDate' => $data['start_date'] ?? null,
                                    'endDate' => $data['end_date'] ?? null,
                                ])->setPaper('A4', 'landscape');

                                return response()->streamDownload(
                                    fn() => print($pdf->output()),
                                    'laporan-pendapatan.pdf'
                                );
                            }
                        }

                        // ================= EXCEL =================
                        if ($data['format'] === 'excel') {
                            if ($data['jenis'] === 'booking') {
                                return Excel::download(
                                    new \App\Exports\BookingExport(
                                        $data['start_date'],
                                        $data['end_date']
                                    ),
                                    'laporan-booking.xlsx'
                                );
                            }

                            if ($data['jenis'] === 'pendapatan') {
                                return Excel::download(
                                    new \App\Exports\PendapatanExport(
                                        $data['start_date'],
                                        $data['end_date']
                                    ),
                                    'laporan-pendapatan.xlsx'
                                );
                            }
                        }
                    }),
            ])
            ->defaultSort('bulan', 'desc');
    }

    /**
     * DATA BULANAN (DUMMY)
     * nanti bisa ganti query GROUP BY bulan
     */
    protected function getMonthlyStats(): Collection
    {
        // === BOOKING PER BULAN ===
        $booking = Reservasi::selectRaw('
            YEAR(tanggal_reservasi) as tahun,
            MONTH(tanggal_reservasi) as bulan,
            COUNT(*) as total,
            SUM(status = "selesai") as selesai,
            SUM(status = "dibatalkan") as batal
        ')
            ->groupBy('tahun', 'bulan')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get()
            ->keyBy(fn($row) => $row->tahun . '-' . $row->bulan);

        // === PENDAPATAN PER BULAN ===
        $pendapatan = RekamMedis::selectRaw('
            YEAR(tanggal_pemeriksaan) as tahun,
            MONTH(tanggal_pemeriksaan) as bulan,
            SUM(biaya) as pendapatan
        ')
            ->groupBy('tahun', 'bulan')
            ->get()
            ->keyBy(fn($row) => $row->tahun . '-' . $row->bulan);

        // === GABUNGKAN DATA ===
        return $booking->map(function ($row, $key) use ($pendapatan) {
            return [
                'bulan' => Carbon::create($row->tahun, $row->bulan)
                    ->translatedFormat('F Y'),

                'total' => (int) $row->total,
                'selesai' => (int) $row->selesai,
                'batal' => (int) $row->batal,
                'pendapatan' => (int) ($pendapatan[$key]->pendapatan ?? 0),
            ];
        })->values();
    }
}

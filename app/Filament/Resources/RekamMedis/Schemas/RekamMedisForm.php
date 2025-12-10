<?php

namespace App\Filament\Resources\RekamMedis\Schemas;

use App\Models\Pasien;
use App\Models\RekamMedis;
use App\Models\Reservasi;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RekamMedisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Card 1: Nomor Rekam Medis
                Section::make('📋 Nomor Rekam Medis')
                    ->description('Nomor rekam medis akan digenerate otomatis')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_rekam')
                            ->label('Nomor Rekam Medis')
                            ->default(fn() => RekamMedis::generateNomorRekam())
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-hashtag')
                            ->prefixIconColor('primary')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                // Card 2: Informasi Pasien & Dokter
                Section::make('👥 Informasi Pasien & Dokter')
                    ->schema([
                        Forms\Components\Select::make('pasien_id')
                            ->label('Pasien')
                            ->options(Pasien::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->columnSpan(2), // Bukan columnSpanFull

                        Forms\Components\Select::make('dokter_id')
                            ->label('Dokter Pemeriksa')
                            ->columnSpan(1), // Ambil 1 kolom saja

                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')
                            ->label('Tanggal Pemeriksaan')
                            ->columnSpan(1), // Ambil 1 kolom saja

                        Forms\Components\Select::make('reservasi_id')
                            ->label('Reservasi (Opsional)')
                            ->columnSpan(2), // 2 kolom
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                // Card 3: Anamnesis & Pemeriksaan
                Section::make('🩺 Anamnesis & Pemeriksaan Fisik')
                    ->description('Riwayat keluhan dan hasil pemeriksaan fisik')
                    ->schema([
                        Forms\Components\Textarea::make('anamnesis')
                            ->label('Anamnesis (Keluhan/Riwayat Penyakit)')
                            ->required()
                            ->rows(5)
                            ->placeholder('Tuliskan keluhan pasien, riwayat penyakit sekarang, riwayat penyakit dahulu, dan informasi relevan lainnya...')
                            ->helperText('⚕️ Catat dengan detail untuk dokumentasi medis yang baik')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('pemeriksaan_fisik')
                            ->label('Pemeriksaan Fisik')
                            ->rows(5)
                            ->placeholder('Tuliskan hasil pemeriksaan fisik: tanda vital, pemeriksaan head to toe, dan temuan klinis...')
                            ->helperText('Hasil pemeriksaan fisik yang dilakukan')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Card 4: Diagnosis & Terapi
                Section::make('💊 Diagnosis & Terapi')
                    ->description('Diagnosis dan rencana terapi/tindakan')
                    ->schema([
                        Forms\Components\Textarea::make('diagnosis')
                            ->label('Diagnosis')
                            ->required()
                            ->rows(4)
                            ->placeholder('Tuliskan diagnosis kerja atau diagnosis banding...')
                            ->helperText('🔍 Diagnosis berdasarkan anamnesis dan pemeriksaan')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('terapi')
                            ->label('Terapi/Tindakan')
                            ->rows(4)
                            ->placeholder('Tuliskan terapi atau tindakan medis yang diberikan...')
                            ->helperText('Rencana terapi dan tindakan medis')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('resep_obat')
                            ->label('Resep Obat')
                            ->rows(5)
                            ->placeholder('Tuliskan resep obat dengan detail: nama obat, dosis, frekuensi, dan durasi...')
                            ->helperText('💊 Contoh: Amoxicillin 500mg, 3x1, selama 5 hari')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                // Card 5: Catatan & Biaya
                Section::make('📝 Catatan & Biaya')
                    ->description('Catatan tambahan dan biaya pemeriksaan')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->placeholder('Catatan penting atau instruksi khusus untuk follow-up...')
                            ->helperText('Informasi tambahan yang perlu diperhatikan')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('biaya')
                            ->label('Biaya Pemeriksaan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required()
                            ->minValue(0)
                            ->maxValue(99999999.99)
                            ->step(1000)
                            ->prefixIcon('heroicon-m-banknotes')
                            ->prefixIconColor('warning')
                            ->helperText('Total biaya pemeriksaan dan tindakan')
                            ->placeholder('0'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

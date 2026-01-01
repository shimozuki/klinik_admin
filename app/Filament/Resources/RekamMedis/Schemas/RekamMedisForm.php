<?php

namespace App\Filament\Resources\RekamMedis\Schemas;

use App\Models\Pasien;
use App\Models\RekamMedis;
use App\Models\Reservasi;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RekamMedisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([

            /* ======================
             | NOMOR REKAM MEDIS
             ====================== */
            Section::make('📋 Nomor Rekam Medis')
                ->schema([
                    Forms\Components\TextInput::make('nomor_rekam')
                        ->default(fn() => RekamMedis::generateNomorRekam())
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                ]),

            /* ======================
             | PASIEN & DOKTER
             ====================== */
            Section::make('👥 Informasi Pasien & Dokter')
                ->schema([
                    Forms\Components\Select::make('pasien_id')
                        ->label('Pasien')
                        ->options(
                            Pasien::with('user')
                                ->get()
                                ->pluck('user.name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('dokter_id')
                        ->options(User::role('dokter')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal_pemeriksaan')
                        ->required()
                        ->minDate(today()),

                    Forms\Components\Select::make('reservasi_id')
                        ->options(
                            Reservasi::whereIn('status', ['dikonfirmasi', 'selesai'])
                                ->pluck('nomor_reservasi', 'id')
                        )
                        ->searchable()
                        ->preload(),
                ])
                ->columns(2),

            /* ======================
             | TANDA VITAL
             ====================== */
            Section::make('❤️ Tanda Vital')
                ->schema([
                    Forms\Components\TextInput::make('tekanan_darah')->placeholder('120/80'),
                    Forms\Components\TextInput::make('detak_jantung')->numeric()->suffix('bpm'),
                    Forms\Components\TextInput::make('suhu')->numeric()->suffix('°C'),
                    Forms\Components\TextInput::make('berat_badan')->numeric()->suffix('kg'),
                ])
                ->columns(2),

            /* ======================
             | ANAMNESIS & DIAGNOSIS
             ====================== */
            Section::make('🩺 Anamnesis & Diagnosis')
                ->schema([
                    Forms\Components\Textarea::make('anamnesis')
                        ->required()
                        ->rows(5)
                        ->placeholder(
                            "Keluhan utama:\n" .
                                "Riwayat penyakit sekarang:\n" .
                                "Riwayat penyakit dahulu:\n" .
                                "Riwayat alergi:\n" .
                                "Catatan tambahan:"
                        ),

                    Forms\Components\Textarea::make('diagnosis')
                        ->required()
                        ->rows(4)
                        ->placeholder('Tuliskan diagnosis kerja atau diagnosis banding...'),
                ])
                ->columns(1),

            /* ======================
             | 💊 RESEP OBAT (REPEATER)
             ====================== */
            Section::make('💊 Resep Obat')
                ->description('Tambahkan satu atau lebih obat')
                ->schema([
                    Repeater::make('resepDetail')
                        ->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('nama_obat')
                                ->required()
                                ->placeholder('Contoh: Asam Mefenamat 500mg'),

                            Forms\Components\TextInput::make('dosis')
                                ->required()
                                ->placeholder('1 tablet / 10–15 ml'),

                            Forms\Components\TextInput::make('frekuensi')
                                ->required()
                                ->placeholder('3x sehari setelah makan'),

                            Forms\Components\TextInput::make('durasi')
                                ->required()
                                ->placeholder('3 hari'),

                            Forms\Components\Textarea::make('catatan')
                                ->rows(2)
                                ->placeholder('Catatan tambahan (opsional)'),
                        ])
                        ->columns(2)
                        ->addActionLabel('➕ Tambah Obat')
                        ->reorderable(false),
                ]),

            Section::make('📝 Tindakan & Biaya')
                ->schema([
                    Forms\Components\Textarea::make('treatment')
                        ->label('Perawatan/Tindakan')
                        ->required()
                        ->rows(3)
                        ->placeholder('Tuliskan tindakan atau perawatan yang dilakukan...'),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Dokter')
                        ->required()
                        ->rows(3)
                        ->placeholder('Catatan Dokter'),
                    Forms\Components\TextInput::make('biaya_treatment')
                        ->label('Biaya Tindakan')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $set(
                                'biaya',
                                ($state ?? 0) + ($get('biaya_konsultasi') ?? 0)
                            );
                        }),

                    Forms\Components\TextInput::make('biaya_konsultasi')
                        ->label('Biaya Konsultasi')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $set(
                                'biaya',
                                ($get('biaya_treatment') ?? 0) + ($state ?? 0)
                            );
                        }),

                    Forms\Components\TextInput::make('biaya')
                        ->label('Total Biaya')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(),

                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }
}

<?php

namespace App\Filament\Resources\KonsultasiOnline\Schemas;

use App\Models\KonsultasiOnline;
use App\Models\Pasien;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class KonsultasiOnlineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Card 1: Nomor Konsultasi
                Section::make('📱 Nomor Konsultasi')
                    ->description('Nomor konsultasi akan digenerate otomatis')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_konsultasi')
                            ->label('Nomor Konsultasi')
                            ->default(fn() => KonsultasiOnline::generateNomorKonsultasi())
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
                    ->description('Data pasien dan dokter yang menangani')
                    ->schema([
                        Forms\Components\Select::make('pasien_id')
                            ->label('Pasien')
                            ->options(Pasien::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-m-user')
                            ->helperText('Pilih pasien yang akan konsultasi')
                            ->disabled(fn($context) => $context === 'edit')
                            ->dehydrated()
                            ->columnSpan(2),

                        Forms\Components\Select::make('dokter_id')
                            ->label('Dokter')
                            ->options(User::role('dokter')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->suffixIcon('heroicon-m-user-circle')
                            ->helperText('Dokter yang menangani konsultasi')
                            ->placeholder('Belum ditentukan')
                            ->disabled(
                                fn(Get $get, $context) =>
                                $context === 'edit' &&
                                    !empty($get('dokter_id')) &&
                                    $get('dokter_id') !== auth()->id()
                            )
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status Konsultasi')
                            ->required()
                            ->options([
                                'menunggu' => 'Menunggu',
                                'berlangsung' => 'Berlangsung',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('menunggu')
                            ->native(false)
                            ->prefixIcon('heroicon-m-clock')
                            ->live()
                            ->disabled(
                                fn($context, $record) =>
                                $context === 'edit' &&
                                    in_array($record?->status, ['selesai', 'dibatalkan'])
                            )
                            ->helperText(fn(Get $get) => match ($get('status')) {
                                'menunggu' => '⏳ Menunggu dokter',
                                'berlangsung' => '💬 Sedang berlangsung',
                                'selesai' => '✅ Telah selesai',
                                'dibatalkan' => '❌ Dibatalkan',
                                default => 'Pilih status'
                            })
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                // Card 3: Keluhan Pasien
                Section::make('💬 Keluhan Pasien')
                    ->description('Detail keluhan yang disampaikan pasien')
                    ->schema([
                        Forms\Components\Textarea::make('keluhan')
                            ->label('Keluhan')
                            ->required()
                            ->rows(5)
                            ->placeholder('Tuliskan keluhan atau pertanyaan yang ingin dikonsultasikan...')
                            ->helperText('📝 Jelaskan keluhan dengan detail untuk mendapat konsultasi yang tepat')
                            ->disabled(fn($context) => $context === 'edit')
                            ->dehydrated()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                // Card 4: Waktu & Biaya
                Section::make('💰 Waktu & Biaya Konsultasi')
                    ->description('Waktu pelaksanaan dan biaya konsultasi')
                    ->schema([
                        Forms\Components\DateTimePicker::make('dimulai_pada')
                            ->label('Dimulai Pada')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->prefixIcon('heroicon-m-play')
                            ->prefixIconColor('success')
                            ->helperText('Waktu konsultasi dimulai')
                            ->disabled(fn(Get $get) => $get('status') === 'menunggu')
                            ->required(fn(Get $get) => in_array($get('status'), ['berlangsung', 'selesai']))
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('selesai_pada')
                            ->label('Selesai Pada')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->prefixIcon('heroicon-m-stop')
                            ->prefixIconColor('danger')
                            ->helperText('Waktu konsultasi selesai')
                            ->disabled(fn(Get $get) => in_array($get('status'), ['menunggu', 'berlangsung']))
                            ->required(fn(Get $get) => $get('status') === 'selesai')
                            ->after('dimulai_pada')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('biaya_konsultasi')
                            ->label('Biaya Konsultasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required()
                            ->minValue(0)
                            ->maxValue(9999999.99)
                            ->step(1000)
                            ->prefixIcon('heroicon-m-banknotes')
                            ->prefixIconColor('warning')
                            ->helperText('Total biaya konsultasi online')
                            ->placeholder('0')
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(
                        fn($context, $record) =>
                        $context === 'create' ||
                            ($context === 'edit' && $record?->status === 'menunggu')
                    ),

                // Card 5: Catatan Dokter
                Section::make('📋 Catatan Dokter')
                    ->description('Catatan, diagnosis, atau saran dari dokter')
                    ->schema([
                        Forms\Components\Textarea::make('catatan_dokter')
                            ->label('Catatan Dokter')
                            ->rows(6)
                            ->placeholder('Tuliskan diagnosis, saran, atau rekomendasi untuk pasien...')
                            ->helperText(fn(Get $get) => match ($get('status')) {
                                'menunggu' => '⚠️ Catatan dapat diisi setelah konsultasi dimulai',
                                'berlangsung' => '⚕️ Isi catatan selama atau setelah konsultasi',
                                'selesai' => '✅ Catatan ini akan dilihat oleh pasien',
                                'dibatalkan' => '❌ Konsultasi dibatalkan',
                                default => '⚕️ Catatan untuk pasien'
                            })
                            ->disabled(
                                fn(Get $get, $context) =>
                                $get('status') === 'menunggu' ||
                                    ($context === 'edit' && $get('status') === 'selesai')
                            )
                            ->required(fn(Get $get) => $get('status') === 'selesai')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(
                        fn($context, $record) =>
                        $context === 'create' ||
                            ($context === 'edit' && empty($record?->catatan_dokter))
                    ),
            ]);
    }
}

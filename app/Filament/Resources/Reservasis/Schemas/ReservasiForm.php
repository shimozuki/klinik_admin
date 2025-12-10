<?php

namespace App\Filament\Resources\Reservasis\Schemas;

use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\Reservasi;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Card 1: Nomor Reservasi
                Section::make('📋 Nomor Reservasi')
                    ->description('Nomor reservasi akan digenerate otomatis')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_reservasi')
                            ->label('Nomor Reservasi')
                            ->default(fn() => Reservasi::generateNomorReservasi())
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

                // Card 2: Data Pasien
                Section::make('👤 Data Pasien')
                    ->description('Pilih pasien yang akan melakukan reservasi')
                    ->schema([
                        Forms\Components\Select::make('pasien_id')
                            ->label('Pasien')
                            ->options(Pasien::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-m-user')
                            ->helperText('Pilih nama pasien dari daftar')
                            ->placeholder('Cari dan pilih pasien...')
                            ->live()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                // Card 3: Pilih Dokter & Jadwal
                Section::make('👨‍⚕️ Dokter & Jadwal')
                    ->description('Pilih dokter dan jadwal praktek')
                    ->schema([
                        Forms\Components\Select::make('dokter_id')
                            ->label('Dokter')
                            ->options(User::role('dokter')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-m-user-circle')
                            ->helperText('Pilih dokter yang akan dikonsultasi')
                            ->placeholder('Cari dan pilih dokter...')
                            ->live()
                            ->afterStateUpdated(fn($set) => $set('jadwal_id', null)),

                        Forms\Components\Select::make('jadwal_id')
                            ->label('Jadwal Praktek')
                            ->options(function (callable $get) {
                                $dokterId = $get('dokter_id');
                                if (!$dokterId) {
                                    return [];
                                }
                                return JadwalDokter::where('dokter_id', $dokterId)
                                    ->where('status_aktif', true)
                                    ->get()
                                    ->mapWithKeys(function ($jadwal) {
                                        return [
                                            $jadwal->id => $jadwal->hari . ' (' .
                                                date('H:i', strtotime($jadwal->jam_mulai)) . ' - ' .
                                                date('H:i', strtotime($jadwal->jam_selesai)) . ')'
                                        ];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->prefixIconColor('success')
                            ->helperText('Pilih hari dan jam praktek')
                            ->placeholder('Pilih jadwal...')
                            ->disabled(fn(callable $get) => !$get('dokter_id'))
                            ->live(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                // Card 4: Tanggal & Waktu Reservasi
                Section::make('📅 Tanggal & Waktu')
                    ->description('Tentukan tanggal dan jam reservasi')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_reservasi')
                            ->label('Tanggal Reservasi')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(now())
                            ->prefixIcon('heroicon-m-calendar')
                            ->prefixIconColor('primary')
                            ->helperText('Pilih tanggal kunjungan')
                            ->closeOnDateSelection(),

                        Forms\Components\TimePicker::make('jam_reservasi')
                            ->label('Jam Reservasi')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->displayFormat('H:i')
                            ->prefixIcon('heroicon-m-clock')
                            ->prefixIconColor('info')
                            ->helperText('Pilih jam kunjungan')
                            ->placeholder('00:00'),

                        Forms\Components\TextInput::make('nomor_antrian')
                            ->label('Nomor Antrian')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->prefixIcon('heroicon-m-queue-list')
                            ->prefixIconColor('warning')
                            ->helperText('Nomor urut antrian pasien'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(false),

                // Card 5: Keluhan & Catatan
                Section::make('📝 Keluhan & Catatan')
                    ->description('Keluhan pasien dan catatan tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('keluhan')
                            ->label('Keluhan Pasien')
                            ->rows(4)
                            ->placeholder('Tuliskan keluhan atau gejala yang dirasakan pasien...')
                            ->helperText('Deskripsikan keluhan dengan jelas untuk membantu dokter')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->placeholder('Catatan khusus atau informasi tambahan (opsional)...')
                            ->helperText('Informasi tambahan yang perlu diketahui')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),

                // Card 6: Status & Konfirmasi
                Section::make('⚙️ Status Reservasi')
                    ->description('Status dan informasi konfirmasi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'menunggu' => 'Menunggu',
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('menunggu')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-m-signal')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'dikonfirmasi') {
                                    $set('dikonfirmasi_pada', now());
                                } elseif ($state === 'dibatalkan') {
                                    $set('dibatalkan_pada', now());
                                }
                            }),

                        Forms\Components\DateTimePicker::make('dikonfirmasi_pada')
                            ->label('Dikonfirmasi Pada')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn(callable $get) => $get('status') === 'dikonfirmasi'),

                        Forms\Components\DateTimePicker::make('dibatalkan_pada')
                            ->label('Dibatalkan Pada')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn(callable $get) => $get('status') === 'dibatalkan'),

                        Forms\Components\TextInput::make('alasan_batal')
                            ->label('Alasan Pembatalan')
                            ->maxLength(255)
                            ->placeholder('Tuliskan alasan pembatalan...')
                            ->visible(fn(callable $get) => $get('status') === 'dibatalkan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

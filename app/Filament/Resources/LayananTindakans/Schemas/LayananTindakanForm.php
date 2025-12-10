<?php

namespace App\Filament\Resources\LayananTindakans\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LayananTindakanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Card 1: Informasi Layanan
                Section::make('🏥 Informasi Layanan')
                    ->description('Data dasar layanan tindakan medis')
                    ->schema([
                        Forms\Components\TextInput::make('kode')
                            ->label('Kode Layanan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('LYN-001')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->prefixIconColor('primary')
                            ->helperText('Kode unik untuk identifikasi layanan')
                            ->alphaDash(),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Layanan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Konsultasi Umum')
                            ->prefixIcon('heroicon-m-clipboard-document-check')
                            ->prefixIconColor('success')
                            ->helperText('Nama layanan tindakan medis')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Layanan')
                            ->rows(4)
                            ->placeholder('Tuliskan deskripsi detail mengenai layanan tindakan ini...')
                            ->helperText('Penjelasan lengkap tentang layanan yang diberikan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                // Card 2: Harga & Durasi
                Section::make('💰 Harga & Estimasi Waktu')
                    ->description('Informasi biaya dan durasi layanan')
                    ->schema([
                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Layanan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(99999999.99)
                            ->step(1000)
                            ->placeholder('0')
                            ->prefixIcon('heroicon-m-banknotes')
                            ->prefixIconColor('warning')
                            ->helperText('Harga dalam Rupiah'),

                        Forms\Components\TextInput::make('estimasi_durasi')
                            ->label('Estimasi Durasi')
                            ->numeric()
                            ->suffix('menit')
                            ->minValue(1)
                            ->placeholder('30')
                            ->prefixIcon('heroicon-m-clock')
                            ->prefixIconColor('info')
                            ->helperText('Perkiraan waktu dalam menit'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                // Card 3: Status
                Section::make('⚙️ Status Layanan')
                    ->description('Aktifkan atau nonaktifkan layanan')
                    ->schema([
                        Forms\Components\Toggle::make('status_aktif')
                            ->label('Status Aktif')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-x-circle')
                            ->helperText('Aktifkan layanan agar dapat digunakan dalam transaksi')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

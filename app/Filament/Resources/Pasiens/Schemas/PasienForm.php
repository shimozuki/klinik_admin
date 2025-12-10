<?php

// app/Filament/Resources/Pasiens/Schemas/PasienForm.php

namespace App\Filament\Resources\Pasiens\Schemas;

use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PasienForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Card 1: Informasi Akun User
                Section::make('👤 Informasi Akun')
                    ->description('Data akun user yang terhubung dengan pasien')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pilih User')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama lengkap')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(User::class, 'email')
                                    ->maxLength(255)
                                    ->placeholder('contoh@email.com'),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->revealable()
                                    ->placeholder('Minimal 8 karakter'),
                            ])
                            ->createOptionModalHeading('Buat User Baru')
                            ->editOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password Baru (Kosongkan jika tidak ingin mengubah)')
                                    ->password()
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->revealable()
                                    ->dehydrated(fn($state) => filled($state)),
                            ])
                            ->native(false)
                            ->suffixIcon('heroicon-m-user')
                            ->helperText('Pilih user yang sudah ada atau buat user baru')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                // Card 2: Data Identitas Pasien
                Section::make('🆔 Data Identitas')
                    ->description('Nomor rekam medis dan identitas pasien')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_rekam_medis')
                            ->label('Nomor Rekam Medis')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('RM-2024-0001')
                            ->helperText('Contoh: RM-TAHUN-NOMOR')
                            ->prefixIcon('heroicon-m-document-text')
                            ->prefixIconColor('success'),

                        Forms\Components\TextInput::make('nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->unique(ignoreRecord: true)
                            ->maxLength(16)
                            ->length(16)
                            ->numeric()
                            ->placeholder('1234567890123456')
                            ->prefixIcon('heroicon-m-identification')
                            ->prefixIconColor('info'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                // Card 3: Data Pribadi
                Section::make('📋 Data Pribadi')
                    ->description('Informasi pribadi dan biodata pasien')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Pilih tanggal lahir')
                            ->prefixIcon('heroicon-m-cake')
                            ->prefixIconColor('warning')
                            ->closeOnDateSelection(),

                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->required()
                            ->options([
                                'Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->native(false)
                            ->prefixIcon('heroicon-m-user'),

                        Forms\Components\Select::make('golongan_darah')
                            ->label('Golongan Darah')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'AB' => 'AB',
                                'O' => 'O',
                                'A+' => 'A+',
                                'A-' => 'A-',
                                'B+' => 'B+',
                                'B-' => 'B-',
                                'AB+' => 'AB+',
                                'AB-' => 'AB-',
                                'O+' => 'O+',
                                'O-' => 'O-',
                            ])
                            ->searchable()
                            ->native(false)
                            ->prefixIcon('heroicon-m-beaker')
                            ->prefixIconColor('danger')
                            ->placeholder('golongan darah'),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->placeholder('Masukkan alamat lengkap dengan RT/RW, Kelurahan, Kecamatan, Kota')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(false),

                // Card 4: Kontak
                Section::make('📞 Informasi Kontak')
                    ->description('Nomor telepon dan kontak darurat')
                    ->schema([
                        Forms\Components\TextInput::make('telepon')
                            ->label('Nomor Telepon')
                            ->required()
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('08123456789')
                            ->prefixIcon('heroicon-m-phone')
                            ->prefixIconColor('success')
                            ->helperText('Nomor yang bisa dihubungi'),

                        Forms\Components\TextInput::make('kontak_darurat')
                            ->label('Nama Kontak Darurat')
                            ->maxLength(255)
                            ->placeholder('Nama keluarga atau kerabat')
                            ->prefixIcon('heroicon-m-user-group')
                            ->prefixIconColor('danger'),

                        Forms\Components\TextInput::make('telepon_darurat')
                            ->label('Telepon Darurat')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('08123456789')
                            ->prefixIcon('heroicon-m-phone')
                            ->prefixIconColor('danger')
                            ->helperText('Nomor yang bisa dihubungi saat darurat'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Card 5: Riwayat Medis
                Section::make('🏥 Riwayat Medis')
                    ->description('Informasi kesehatan dan riwayat alergi')
                    ->schema([
                        Forms\Components\Textarea::make('alergi')
                            ->label('Riwayat Alergi')
                            ->rows(4)
                            ->placeholder('Tuliskan riwayat alergi pasien seperti alergi obat-obatan, makanan, atau lainnya. Kosongkan jika tidak ada.')
                            ->helperText('⚠️ Informasi ini sangat penting untuk keamanan pengobatan')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

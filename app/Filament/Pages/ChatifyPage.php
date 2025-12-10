<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ChatifyPage extends Page
{
    protected string $view = 'filament.pages.chatify-page';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationLabel(): string
    {
        return 'Chat';
    }

    public static function getNavigationUrl(): string
    {
        return url('/chatify');
    }

    public static function shouldOpenUrlInNewTab(): bool
    {
        return false; // true jika ingin buka tab baru
    }
}

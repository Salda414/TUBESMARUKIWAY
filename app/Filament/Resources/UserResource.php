<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


// tambahan untuk form dan kolom tabel

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\BadgeColumn;

// tambahan untuk user exporter
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\UserExporter;

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    // tambahan untuk pengelompokan
    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                
                    TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('email')
                        ->email()
                        ->label('Email address')
                        ->required()
                        ->maxLength(100),    
                    TextInput::make('password')
                        ->password()
                        // ->required(fn (Forms\Form $form): bool => $form->getLivewire() instanceof Pages\CreateUser)
                        ->minLength(8)
                        ->same('password_confirmation')
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->label('Password Confirmation')
                        // ->required(fn (Forms\Form $form): bool => $form->getLivewire() instanceof Pages\CreateUser) // ✅ Perbaikan
                        ->minLength(8)
                        ->minlength(8)
                        ->dehydrated(false),
                    Select::make('user_group')
                        ->options([
                            'admin' => 'admin',
                            'customer' => 'customer',
                        ])
                        ->default('customer')

                        ->default('customer'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                BadgeColumn::make('user_group')
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'warning',
                        'customer' => 'success',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            // tombol tambahan
            ->headerActions([
                // tombol tambahan export csv dan excel
                ExportAction::make()->exporter(UserExporter::class)->color('success'),
                // tombol tambahan export pdf
                // ✅ Tombol Unduh PDF
                Action::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $users = User::all();

                    $pdf = Pdf::loadView('pdf.users', ['users' => $users]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'user-list.pdf'
                    );
                })
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                // tambahan untuk export excel
                ExportBulkAction::make()->exporter(UserExporter::class)

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
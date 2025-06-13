<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Models\Department;
use App\Models\Division;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->can('student_view');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('roll_number')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive(),
                Forms\Components\Select::make('division_id')
                ->label('Division')
                ->options(function ($get) {
                    $departmentId = $get('department_id');
                    if (!$departmentId) {
                        return [];
                    }
                    return \App\Models\Division::where('department_id', $departmentId)
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->required()
                ->searchable()
                ->preload(),
                Forms\Components\TextInput::make('batch')
                    ->label('Batch Year')
                    ->type('number')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\Hidden::make('password')
                    ->default(\Illuminate\Support\Facades\Hash::make('password'))
                    ->dehydrated(true),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make('password'); // Set default password
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['password'])) {
            $data['password'] = Hash::make('password');
        }
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roll_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('division.name')
                    ->label('Division')
                    ->searchable(),
                Tables\Columns\TextColumn::make('batch')
                    ->label('Batch Year')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn ($record) => Auth::check() && Auth::user()->can('student_update')),
                Tables\Actions\DeleteAction::make()->visible(fn ($record) => Auth::check() && Auth::user()->can('student_delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => Auth::check() && Auth::user()->can('student_delete')),
                ]),
            ])
            ->headerActions([
                Action::make('import')
                    ->label('Import Students')
                    ->form([
                        FileUpload::make('csv_file')
                            ->label('CSV File')
                            ->required()
                            ->acceptedFileTypes(['text/csv'])
                            ->maxSize(1024)
                            ->disk('public')
                            ->directory('imports'),
                    ])
                    ->action(function (array $data): void {
                        DB::transaction(function () use ($data) {
                            $path = Storage::disk('public')->path($data['csv_file']);
                            $csv = Reader::createFromPath($path, 'r');
                            $csv->setHeaderOffset(0);

                            foreach ($csv->getRecords() as $record) {
                                Student::create([
                                    'name' => $record['name'],
                                    'email' => $record['email'],
                                    'roll_number' => $record['roll_number'],
                                    'department_id' => $record['department_id'],
                                    'division_id' => $record['division_id'],
                                    'batch' => $record['batch'],
                                    'password' => Hash::make('password'),
                                    'is_active' => true,
                                ]);
                            }

                            // Delete the file after import
                            Storage::disk('public')->delete($data['csv_file']);
                        });
                    }),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

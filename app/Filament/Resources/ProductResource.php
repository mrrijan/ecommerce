<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //this group will take two columns
                Group::make()->schema([
                    Forms\Components\Section::make('Product Information')->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (String $operation , $state , Forms\Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        TextInput::make('slug')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products')

                    ])->columns(2),
                    Forms\Components\Section::make('Images')->schema([
                        Forms\Components\FileUpload::make('images')
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('products')
                            ->reorderable()
                    ])

                ])->columnSpan(2),

                //this will take one column
                Group::make()->schema([
                    Forms\Components\Section::make('price')->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('NPR')
                    ]),
                    Forms\Components\Section::make('Associations')->schema([
                        Select::make('category_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Category')
                            ->relationship('category', 'name'),
                        Select::make('brand_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Brand')
                            ->relationship('brand', 'name')
                    ]),
                    Forms\Components\Section::make('Status')->schema([
                        Forms\Components\Toggle::make('in_stock')
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->required()
                            ->default(false),
                        Forms\Components\Toggle::make('on_sale')
                            ->required()
                            ->default(false),

                    ])
                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('NPR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                ->boolean(),
                Tables\Columns\IconColumn::make('on_sale')
                ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

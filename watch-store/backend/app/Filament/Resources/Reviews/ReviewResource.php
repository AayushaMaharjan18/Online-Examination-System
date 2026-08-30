<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\Schemas\ReviewForm;
use App\Models\Review;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Reviews';

    protected static \UnitEnum|string|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 40;

    public static function getEloquentQuery(): Builder
    {
        // Eager-load so customer/product/order columns render without N+1.
        return parent::getEloquentQuery()->with(['product', 'user', 'order']);
    }

    public static function form(Schema $schema): Schema
    {
        return ReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->words(3),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 2 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('title')->limit(30)->searchable(),
                TextColumn::make('comment')->limit(60)->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Review::STATUS_APPROVED => 'success',
                        Review::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                IconColumn::make('is_visible_on_homepage')
                    ->label('Homepage')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_verified_purchase')
                    ->label('Verified')
                    ->boolean(),
                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Review::STATUS_PENDING => 'Pending',
                        Review::STATUS_APPROVED => 'Approved',
                        Review::STATUS_REJECTED => 'Rejected',
                    ]),
                SelectFilter::make('is_visible_on_homepage')
                    ->label('Homepage visibility')
                    ->options([
                        true => 'Visible on homepage',
                        false => 'Hidden from homepage',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record): bool => $record->status !== Review::STATUS_APPROVED)
                    ->action(fn (Review $record) => $record->update(['status' => Review::STATUS_APPROVED])),
                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record): bool => $record->status !== Review::STATUS_REJECTED)
                    ->action(fn (Review $record) => $record->update([
                        'status' => Review::STATUS_REJECTED,
                        'is_visible_on_homepage' => false,
                    ])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_approve')
                        ->label('Approve selected')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => Review::STATUS_APPROVED])),
                    BulkAction::make('bulk_reject')
                        ->label('Reject selected')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => Review::STATUS_REJECTED,
                            'is_visible_on_homepage' => false,
                        ])),
                    BulkAction::make('bulk_show_home')
                        ->label('Show on homepage')
                        ->icon(Heroicon::OutlinedEye)
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_visible_on_homepage' => true])),
                    BulkAction::make('bulk_hide_home')
                        ->label('Hide from homepage')
                        ->icon(Heroicon::OutlinedEyeSlash)
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_visible_on_homepage' => false])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}


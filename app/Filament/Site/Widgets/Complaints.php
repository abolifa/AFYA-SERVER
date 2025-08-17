<?php

namespace App\Filament\Site\Widgets;

use App\Filament\Site\Resources\ComplaintResource;
use App\Models\Complaint;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Complaints extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('آخر الشكاوى')
            ->description('أحدث 10 شكاوى تم تقديمها')
            ->emptyStateHeading('لا توجد شكاوى')
            ->emptyStateDescription('لا توجد شكاوى حتى الآن.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->query(
                Complaint::query()
                    ->latest()
                    ->limit(10)
            )
            ->recordUrl(fn(Complaint $record) => ComplaintResource::getUrl('view', ['record' => $record], panel: 'site')
            )
            ->columns([
                TextColumn::make('name')->label('الاسم')->placeholder('غير معروف'),
                TextColumn::make('phone')->label('الهاتف')->placeholder('غير معروف')->alignCenter(),
                TextColumn::make('message')->label('الرسالة')->limit(50)->alignCenter()->placeholder('لا توجد رسالة'),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')->alignCenter()->date('d/m/Y'),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->paginated(false);
    }
}

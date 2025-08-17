<?php

namespace App\Filament\Site\Widgets;

use App\Models\Announcement;
use App\Models\Awareness;
use App\Models\Post;
use App\Models\Slider;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStats extends BaseWidget
{
    protected function getStats(): array
    {
        $posts = Post::count();
        $awarenesses = Awareness::count();
        $announcements = Announcement::count();
        $sliders = Slider::count();
        return [
            Stat::make('المقالات', number_format($posts))
                ->color('info')
                ->icon('gmdi-post-add-o')
                ->description('إجمالي عدد المقالات'),
            Stat::make('التوعيات', number_format($awarenesses))
                ->color('success')
                ->icon('fas-hands-holding-circle')
                ->description('إجمالي عدد التوعيات'),
            Stat::make('الإعلانات', number_format($announcements))
                ->color('warning')
                ->icon('fas-bolt-lightning')
                ->description('إجمالي عدد الإعلانات'),
            Stat::make('الشرئح', number_format($sliders))
                ->color('danger')
                ->icon('heroicon-o-rectangle-stack')
                ->description('إجمالي عدد الشرئح'),
        ];
    }
}

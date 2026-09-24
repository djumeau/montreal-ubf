<?php

namespace App\View\Components\ManageSeries;

use App\Models\StudySeries;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeriesCardItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public StudySeries $series,
        public array $rowData, // Data handed to the Edit / Delete modals
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('pages.dashboards.manage-series.series-card-item');
    }
}

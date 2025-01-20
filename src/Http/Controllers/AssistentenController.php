<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\Permission;
use Atom\Core\Models\WebsiteSetting;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class AssistentenController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request): View
    {
        $settings = WebsiteSetting::whereIn('key', ['staff_min_rank', 'min_rank_to_see_hidden_staff'])
            ->pluck('value', 'key');

        $permissions = Permission::with(['users' => fn (Builder $query) => $query->where('hidden_staff', '0')])
            ->whereIn('level', [5, 6])
            ->orderBy('level', 'DESC')
            ->get();

        return view('assistenten', compact('permissions'));
    }
}

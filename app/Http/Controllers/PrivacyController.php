<?php

namespace App\Http\Controllers;

use App\Models\RetentionSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PrivacyController extends Controller
{
    public function __invoke(): View
    {
        try {
            $retention = RetentionSetting::query()->find(1);
        } catch (QueryException $exception) {
            Log::warning('Privacy page could not read retention settings', [
                'exception_class' => $exception::class,
            ]);
            $retention = null;
        }

        return view('privacy', compact('retention'));
    }
}

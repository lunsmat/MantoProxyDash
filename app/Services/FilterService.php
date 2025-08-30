<?php

namespace App\Services;

use App\Models\UrlFilter;
use Illuminate\Support\Facades\Auth;

class FilterService extends Service
{
    public function getAllFilters()
    {
        return UrlFilter::all();
    }

    public function getGroupFilters(int $groupId): mixed
    {
        return UrlFilter::whereHas('groups', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->get();
    }

    public function getGroupFiltersIds(int $groupId): mixed
    {
        return UrlFilter::whereHas('groups', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->pluck('id')->toArray();
    }

    public function registerLog(UrlFilter $filter, string $message, mixed $context = null): void
    {
        if (!$this->log) return;

        $userId = Auth::user()?->id;

        $filter->systemLog()->create([
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => $userId,
        ]);
    }
}

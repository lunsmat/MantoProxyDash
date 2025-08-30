<?php

namespace App\Services;

use App\Models\UrlFilter;

class FilterService
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
}

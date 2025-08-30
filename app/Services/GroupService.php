<?php

namespace App\Services;

use App\Models\Group;
use Illuminate\Support\Facades\Cache;

class GroupService {
    public function detachFilter(Group $group, int $filterId): void {
        $group->filters()->detach($filterId);
    }

    public function attachFilter(Group $group, int $filterId): void {
        $group->filters()->attach($filterId);
    }

    public function findGroupById(int $id): ?Group {
        return Group::find($id)->first();
    }
}

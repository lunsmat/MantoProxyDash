<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Group;
use App\Models\UrlFilter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Device::factory()
            ->count(50)
            ->create();

        Group::factory()
            ->count(10)
            ->create();

        UrlFilter::factory()
            ->count(5)
            ->create();

        Group::all()->each(function (Group $group) {
            $group->devices()->attach(Device::inRandomOrder()->take(rand(1, 30))->pluck('id'));
            $group->filters()->attach(UrlFilter::inRandomOrder()->take(rand(0, 3))->pluck('id'));
        });

        Device::all()->each(function (Device $device) {
            $device->filters()->attach(UrlFilter::inRandomOrder()->take(rand(0, 3))->pluck('id'));
        });
    }
}

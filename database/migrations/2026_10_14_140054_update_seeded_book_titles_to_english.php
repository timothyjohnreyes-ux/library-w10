<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $titles = [
            1 => 'The Quiet Atlas',
            2 => 'A Garden of Small Beginnings',
            3 => 'Letters from the North',
            4 => 'The Last Paper Boat',
            5 => 'A Map of Forgotten Roads',
            6 => 'The House Beyond the Harbor',
            7 => 'Lessons in Starlight',
            8 => 'The Art of Starting Again',
            9 => 'A Season of Open Windows',
            10 => 'The Clockmaker’s Promise',
            11 => 'Stories We Carry Home',
            12 => 'The Orchard at Midnight',
            13 => 'A River Between Us',
            14 => 'The Blue Room',
            15 => 'Notes from a Long Journey',
            16 => 'The Library of Lost Names',
            17 => 'When the Rain Came',
            18 => 'The Lighthouse Keeper',
            19 => 'A Thousand Ordinary Days',
            20 => 'The Shape of Tomorrow',
            21 => 'The Bookshop on Willow Street',
            22 => 'A Winter Without Maps',
            23 => 'The Sound of Distant Trains',
            24 => 'An Honest Place',
            25 => 'The Maker of Morning',
            26 => 'A Study in Quiet Courage',
            27 => 'The House of Green Leaves',
            28 => 'Between Pages and Places',
            29 => 'The Long Way Home',
            30 => 'A Guide to Hidden Rivers',
            31 => 'The Evening Reading Club',
            32 => 'A Sky Full of Questions',
            33 => 'The Keeper of Small Things',
            34 => 'The Road to Clear Water',
            35 => 'The Museum of Possible Futures',
            36 => 'A Letter to the Next Reader',
            37 => 'The Language of Windows',
            38 => 'The Day We Found the Garden',
            39 => 'A Notebook for New Stories',
            40 => 'The Light Between Shelves',
        ];

        foreach ($titles as $id => $title) {
            DB::table('books')
                ->where('id', $id)
                ->update(['title' => $title]);
        }
    }

    public function down(): void
    {
        // Original Faker titles were not meaningful catalogue data.
    }
};

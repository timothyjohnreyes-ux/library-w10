<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('en_US');

        // Create one account for each role.
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $librarian = User::factory()->create([
            'name' => 'Librarian User',
            'email' => 'librarian@example.com',
            'role' => 'librarian',
        ]);

        $memberUser = User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'role' => 'member',
        ]);

        // Create 10 English-language authors.
        $authors = collect();

        for ($i = 0; $i < 10; $i++) {
            $authors->push(Author::create([
                'name' => $faker->name(),
            ]));
        }

        // Create 4 books for every author.
        $books = collect();

        foreach ($authors as $author) {
            for ($i = 1; $i <= 4; $i++) {
                $books->push(Book::create([
                    'author_id' => $author->id,
                    'isbn' => $faker->unique()->numerify('#############'),
                    'title' => $faker->sentence(4),
                    'published_year' => $faker->numberBetween(1950, 2025),
                    'is_reference' => $faker->boolean(20),
                    'cover_path' => null,
                ]));
            }
        }

        // Create 30 members.
        // Link the member login account to one member record.
        $members = collect();

        $members->push(Member::create([
            'user_id' => $memberUser->id,
            'name' => $memberUser->name,
            'email' => $memberUser->email,
        ]));

        for ($i = 1; $i < 30; $i++) {
            $members->push(Member::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
            ]));
        }

        // Create 1–3 loans per member.
        foreach ($members as $member) {
            $loanCount = $faker->numberBetween(1, 3);

            $selectedBooks = $books
                ->shuffle()
                ->take($loanCount);

            foreach ($selectedBooks as $book) {
                DB::table('book_member')->insert([
                    'book_id' => $book->id,
                    'member_id' => $member->id,
                    'borrowed_at' => now()->subDays(
                        $faker->numberBetween(1, 90)
                    ),
                    // About 20% are still unreturned.
                    'returned_at' => $faker->boolean(20)
                        ? null
                        : now()->subDays($faker->numberBetween(0, 30)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

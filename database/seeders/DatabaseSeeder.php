<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Book::insert([
            ['title' => 'Tõde ja õigus', 'author' => 'A. H. Tammsaare', 'publication_year' => 1926, 'genre' => 'Romaan', 'description' => 'Eesti kirjanduse suurim romaan, viieosaline eepos.', 'isbn' => '9789985323456', 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Kevade', 'author' => 'Oskar Luts', 'publication_year' => 1912, 'genre' => 'Romaan', 'description' => 'Armas koolilugu Paunvere küla poistest.', 'isbn' => null, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'The Lord of the Rings', 'author' => 'J. R. R. Tolkien', 'publication_year' => 1954, 'genre' => 'Fantaasia', 'description' => 'Epohhaalne fantaasiatriloogia Keskmisest Maast.', 'isbn' => '9780261102385', 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Dune', 'author' => 'Frank Herbert', 'publication_year' => 1965, 'genre' => 'Ulme', 'description' => 'Klassikaline ulme-eepika kõrbeplaneedil Arrakis.', 'isbn' => '9780441013593', 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => '1984', 'author' => 'George Orwell', 'publication_year' => 1949, 'genre' => 'Düstoopia', 'description' => 'Totalitaarse ühiskonna kujutamine tulevikus.', 'isbn' => '9780451524935', 'image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

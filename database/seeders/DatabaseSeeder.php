<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PaymentTypeSeeder;
use QtyTypeSeeder;
use TransactionTypeSeeder;
use UserRoleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TransactionTypeSeeder::class,
            PaymentTypeSeeder::class,
            QtyTypeSeeder::class,
            UserRoleSeeder::class,
        ]);
    }
}

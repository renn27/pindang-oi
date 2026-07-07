<?php

namespace Database\Factories;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{
    protected $model = Pegawai::class;

    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_pegawai' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'alamat' => fake()->address(),
            'jabatan' => 'Staff',
            'active_role' => 'Anggota Tim',
            'is_active' => true,
            'todo_reminder_enabled' => false,
            'remember_token' => Str::random(10),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = [];

        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'nama' => "Guru $i",
                'username' => "user$i",
                'email' => "user$i@example.com",
                'password' => Hash::make('user$123'),
                'role' => 'user'
            ];
        }

        DB::table('users')->insert($users);

        DB::table('mapel')->insert([
            ['nama' => 'Matematika'],
            ['nama' => 'Bahasa Indonesia'],
            ['nama' => 'Bahasa Inggris'],
            ['nama' => 'IPA'],
            ['nama' => 'IPS'],
            ['nama' => 'PKN'],
            ['nama' => 'Seni Budaya'],
            ['nama' => 'Penjaskes'],
        ]);

        DB::table('kelas')->insert([
            ['nama' => 'Kelas 7A', 'tingkatan' => '7', 'user_id' => 4],
            ['nama' => 'Kelas 7B', 'tingkatan' => '7', 'user_id' => 5],
            ['nama' => 'Kelas 8A', 'tingkatan' => '8', 'user_id' => 6],
            ['nama' => 'Kelas 8B', 'tingkatan' => '8', 'user_id' => 7],
            ['nama' => 'Kelas 9A', 'tingkatan' => '9', 'user_id' => 8],
            ['nama' => 'Kelas 9B', 'tingkatan' => '9', 'user_id' => 9],
        ]);

        DB::table('siswa')->insert([
            ['nama' => 'Ayu Setiawan', 'gender' => 'P', 'nis' => '10001', 'no_telp' => '081234567890', 'walimurid' => 'Budi Setiawan', 'alamat' => 'Jl. Merdeka No.1', 'kelas_id' => 1],
            ['nama' => 'Budi Santoso', 'gender' => 'L', 'nis' => '10002', 'no_telp' => '081234567891', 'walimurid' => 'Siti Santoso', 'alamat' => 'Jl. Merdeka No.2', 'kelas_id' => 1],
            ['nama' => 'Citra Dewi', 'gender' => 'P', 'nis' => '10003', 'no_telp' => '081234567892', 'walimurid' => 'Ahmad Dewi', 'alamat' => 'Jl. Merdeka No.3', 'kelas_id' => 2],
            ['nama' => 'Dodi Prasetyo', 'gender' => 'L', 'nis' => '10004', 'no_telp' => '081234567893', 'walimurid' => 'Eka Prasetyo', 'alamat' => 'Jl. Merdeka No.4', 'kelas_id' => 2],
            ['nama' => 'Eka Rahman', 'gender' => 'L', 'nis' => '10005', 'no_telp' => '081234567894', 'walimurid' => 'Fadli Rahman', 'alamat' => 'Jl. Merdeka No.5', 'kelas_id' => 3],
            ['nama' => 'Fajar Hidayat', 'gender' => 'L', 'nis' => '10006', 'no_telp' => '081234567895', 'walimurid' => 'Gina Hidayat', 'alamat' => 'Jl. Merdeka No.6', 'kelas_id' => 3],
            ['nama' => 'Gita Anggraini', 'gender' => 'P', 'nis' => '10007', 'no_telp' => '081234567896', 'walimurid' => 'Hadi Anggraini', 'alamat' => 'Jl. Merdeka No.7', 'kelas_id' => 4],
            ['nama' => 'Hendra Wijaya', 'gender' => 'L', 'nis' => '10008', 'no_telp' => '081234567897', 'walimurid' => 'Indah Wijaya', 'alamat' => 'Jl. Merdeka No.8', 'kelas_id' => 4],
            ['nama' => 'Indri Lestari', 'gender' => 'P', 'nis' => '10009', 'no_telp' => '081234567898', 'walimurid' => 'Joko Lestari', 'alamat' => 'Jl. Merdeka No.9', 'kelas_id' => 5],
            ['nama' => 'Joko Susanto', 'gender' => 'L', 'nis' => '10010', 'no_telp' => '081234567899', 'walimurid' => 'Kiki Susanto', 'alamat' => 'Jl. Merdeka No.10', 'kelas_id' => 5],
            ['nama' => 'Kartika Sari', 'gender' => 'P', 'nis' => '10011', 'no_telp' => '081234567900', 'walimurid' => 'Lutfi Sari', 'alamat' => 'Jl. Merdeka No.11', 'kelas_id' => 6],
            ['nama' => 'Lukman Hakim', 'gender' => 'L', 'nis' => '10012', 'no_telp' => '081234567901', 'walimurid' => 'Mira Hakim', 'alamat' => 'Jl. Merdeka No.12', 'kelas_id' => 6],
        ]);


        $jadwal = [];
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        for ($i = 0; $i < 6; $i++) {
            $jadwal[] = ['kelas_id' => 1, 'mapel_id' => 1, 'user_id' => 3, 'hari' => $hari[$i], 'mulai' => '08:00:00', 'selesai' => '09:30:00'];
            $jadwal[] = ['kelas_id' => 1, 'mapel_id' => 2, 'user_id' => 5, 'hari' => $hari[$i], 'mulai' => '10:00:00', 'selesai' => '11:30:00'];
            $jadwal[] = ['kelas_id' => 1, 'mapel_id' => 3, 'user_id' => 2, 'hari' => $hari[$i], 'mulai' => '10:00:00', 'selesai' => '11:30:00'];
            $jadwal[] = ['kelas_id' => 1, 'mapel_id' => 4, 'user_id' => 4, 'hari' => $hari[$i], 'mulai' => '11:30:00', 'selesai' => '12:30:00'];
        }

        DB::table('jadwal_kbm')->insert($jadwal);

        $jadwalPiket = [];
        for ($i = 0; $i < 6; $i++) {
            $jadwalPiket[] = ['user_id' => $i + 1, 'tanggal' => now(), 'mulai' => '07:00:00', 'selesai' => '12:00:00'];
        }

        DB::table('jadwal_piket')->insert($jadwalPiket);
    }
}

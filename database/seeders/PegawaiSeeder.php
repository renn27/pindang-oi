<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['achmadawaluddin','achmadawaluddin@example.com','Achmad Awaluddin, S.P., M.E'],
            ['adeulfawahyuni','adeulfawahyuni@example.com','Ade Ulfa Wahyuni, A.Md'],
            ['aisyahputeriumutama','aisyahputeriumutama@example.com','Aisyah Puteri Utama, S.Tr.Stat.'],
            ['akhmadriza','akhmadriza@example.com','Akhmad Riza, SE, M.M.'],
            ['aniyuningsih','aniyuningsih@example.com','Ani Yuningsih, A.Md.'],
            ['astri','astri@example.com','Astri, A.Md.'],
            ['budimartha','budimartha@example.com','Budi Martha, S.E'],
            ['cecepnopriansyah','cecepnopriansyah@example.com','Cecep Nopriansyah, A.Md.'],
            ['deaanisairawan','deaanisairawan@example.com','Dea Anisa Irawan, S.Tr.Stat.'],
            ['efranferikriswanto','efranferikriswanto@example.com','Efran Feri Kriswanto, SST'],
            ['fahria','fahria@example.com','Fahria, SST, M.Si'],
            ['farhansegentaralam','farhansegentaralam@example.com','Farhan Segentar Alam, SE, M.M'],
            ['ferdian','ferdian@example.com','Ferdian'],
            ['gunturteguhiman','gunturteguhiman@example.com','Guntur Teguh Iman, SE, M.Si'],
            ['hendrafebrianto','hendrafebrianto@example.com','Hendra Febrianto, A.Md'],
            ['ifonearma','ifonearma@example.com','Ifone Arma, SE, M.M'],
            ['indahdwipebrianti','indahdwipebrianti@example.com','Indah Dwi Pebrianti, S.Si.'],
            ['indragunawan','indragunawan@example.com','Indra Gunawan, SE'],
            ['irmalina','irmalina@example.com','Irma Lina'],
            ['ishlahulkamal','ishlahulkamal@example.com','Ishlahul Kamal, S.Si.'],
            ['juarsah','juarsah@example.com','Juarsah, SE'],
            ['kurniasih','kurniasih@example.com','Kurniasih, SST'],
            ['lidiaanggitaputri','lidiaanggitaputri@example.com','Lidia Anggita Putri, SST'],
            ['mariaulfa','mariaulfa@example.com','Maria Ulfa, SST'],
            ['meitaayudhia','meitaayudhia@example.com','Meita Ayudhia, SE, M.P.'],
            ['mohrezabahusin','mohrezabahusin@example.com','Moh. Reza Bahusin'],
            ['pusvitasari','pusvitasari@example.com','Pusvitasari, S.Sos., M.P.'],
            ['rahmadi','rahmadi@example.com','Rahmadi'],
            ['rianmaulanasaputra','rianmaulanasaputra@example.com','Rian Maulana Saputra, A.Md.'],
            ['rismakarlia','rismakarlia@example.com','Risma Karlia, SST'],
            ['rismawaty','rismawaty@example.com','Rismawaty, SST, M.E.K.K'],
            ['rosmilyani','rosmilyani@example.com','Rosmilyani, S.M.'],
            ['sapik','sapik@example.com','Sapik'],
            ['sariratnadewi','sariratnadewi@example.com','Sari Ratna Dewi, S.Si.'],
            ['sukendrosuryowiguno','sukendrosuryowiguno@example.com','Sukendro Suryo Wiguno, SST, M.Ec.Dev'],
            ['sulastri','sulastri@example.com','Sulastri, S.Sos.'],
            ['sutarso','sutarso@example.com','Sutarso, ST'],
            ['yulisnurhayani','yulisnurhayani@example.com','Yulis Nurhayani, A.Md., S.E.'],
            ['yurahadi','yurahadi@example.com','Yurahadi, S.E.'],
            ['yolandarizkieaprilia','yolandarizkieaprilia@example.com','Yolanda Rizkie Aprilia'],
            ['sahira','sahira@example.com','Sahira'],
        ];

        foreach ($data as [$username, $email, $nama]) {
            Pegawai::firstOrCreate(
                ['username' => $username],
                [
                    'email'        => $email,
                    'nama_pegawai' => $nama,
                    'password'     => Hash::make('password123'),
                    'jabatan'      => null,
                    'alamat'       => null,
                    'photo'        => null,
                    'active_role'  => null,
                ]
            );
        }
    }
}

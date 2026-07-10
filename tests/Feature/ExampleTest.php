<?php

use App\Models\Pegawai;

it('returns a successful response', function () {
    $user = Pegawai::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});

it('can download ranking anggota pdf', function () {
    $user = Pegawai::factory()->create();

    $response = $this->actingAs($user)->get('/panduan/ranking-anggota/pdf');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

it('can download ranking katim pdf', function () {
    $user = Pegawai::factory()->create();

    $response = $this->actingAs($user)->get('/panduan/ranking-katim/pdf');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

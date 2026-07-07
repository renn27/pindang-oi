<?php

use App\Models\Pegawai;

it('returns a successful response', function () {
    $user = Pegawai::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});

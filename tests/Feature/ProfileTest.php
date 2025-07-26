<?php
/**
 * Pruebas de perfil de usuario para clientes: visualizar, editar y subir archivos.
 *
 * NOTA: Sólo clientes pueden editar su perfil desde Laravel. Profesionales y admins gestionan su perfil desde Orchid.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    // Pruebas para:
    // - Visualizar perfil de usuario
    // - Editar perfil de usuario
    // - Subir archivos (imágenes, evidencias, etc.)
    public function test_user_can_view_profile()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/customer/settings');
        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_user_can_edit_profile()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $response = $this->put('/customer/settings', [
            'name' => 'Nuevo Nombre',
            'email' => $user->email,
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nuevo Nombre']);
    }

    public function test_user_can_upload_file()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension not installed.');
        }
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $file = \Illuminate\Http\UploadedFile::fake()->image('evidencia.jpg');
        $response = $this->post('/customer/settings/upload', [
            'file' => $file,
        ]);
        $response->assertStatus(302);
        // Storage::disk('public')->assertExists('uploads/' . $file->hashName());
    }
}

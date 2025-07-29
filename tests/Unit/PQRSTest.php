<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PQRS;
use App\Models\Customer;

class PQRSTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_pqrs_with_valid_data()
    {
        $customer = Customer::factory()->create();
        $this->actingAs($customer->user);
        $response = $this->post('/pqrs', [
            'subject' => 'Consulta',
            'message' => 'Tengo una duda sobre mi reserva.',
            'email' => $customer->user->email,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pqrs', [
            'subject' => 'Consulta',
            'email' => $customer->user->email,
        ]);
    }

    public function test_cannot_send_pqrs_without_subject_or_message()
    {
        $customer = Customer::factory()->create();
        $this->actingAs($customer->user);
        $response = $this->post('/pqrs', [
            'subject' => '',
            'message' => '',
            'email' => $customer->user->email,
        ]);
        $response->assertSessionHasErrors(['subject', 'message']);
    }

    public function test_email_field_must_be_valid()
    {
        $customer = Customer::factory()->create();
        $this->actingAs($customer->user);
        $response = $this->post('/pqrs', [
            'subject' => 'Consulta',
            'message' => 'Mensaje válido',
            'email' => 'no-valido',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_pqrs_belongs_to_customer()
    {
        $customer = Customer::factory()->create();
        $pqrs = PQRS::factory()->create(['customer_id' => $customer->id]);
        $this->assertEquals($customer->id, $pqrs->customer->id);
    }
}

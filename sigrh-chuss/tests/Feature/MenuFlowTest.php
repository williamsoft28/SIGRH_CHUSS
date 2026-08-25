<?php

namespace Tests\Feature;

use App\Mail\MenuAppliqueMail;
use App\Models\Menu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotelier_can_confirm_a_menu_validated_by_the_prestataire_and_notify_sus_users(): void
    {
        Mail::fake();

        Role::firstOrCreate(['name' => 'service_hotellerie', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sus', 'guard_name' => 'web']);

        $hotel = User::factory()->create();
        $hotel->assignRole('service_hotellerie');

        $sus1 = User::factory()->create();
        $sus1->assignRole('sus');

        $sus2 = User::factory()->create();
        $sus2->assignRole('sus');

        $menu = Menu::create([
            'numero_semaine' => 38,
            'annee' => 2026,
            'date_debut' => Carbon::create(2026, 9, 14),
            'date_fin' => Carbon::create(2026, 9, 20),
            'statut' => 'valide',
            'date_soumission' => now(),
            'date_validation' => now(),
            'nb_modifications' => 0,
        ]);

        $response = $this->actingAs($hotel)
            ->post(route('hotellerie.menus.valider', $menu));

        $response->assertRedirect(route('hotellerie.menus.show', $menu));
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'statut' => 'applique']);

        Mail::assertSent(MenuAppliqueMail::class, 2);
    }
}

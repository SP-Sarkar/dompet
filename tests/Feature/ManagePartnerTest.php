<?php

namespace Tests\Feature;

use App\Partner;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManagePartnerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_see_partner_list_in_partner_index_page()
    {
        $partner = factory(Partner::class)->create();

        $this->loginAsUser();
        $this->visitRoute('partners.index');
        $this->see($partner->name);
    }

    /** @test */
    public function user_can_create_a_partner()
    {
        $this->loginAsUser();
        $this->visitRoute('partners.index');

        $this->click(__('partner.create'));
        $this->seeRouteIs('partners.index', ['action' => 'create']);

        $this->submitForm(__('partner.create'), [
            'name'        => 'Partner 1 name',
            'description' => 'Partner 1 description',
        ]);

        $this->seeRouteIs('partners.index');

        $this->seeInDatabase('partners', [
            'name'        => 'Partner 1 name',
            'description' => 'Partner 1 description',
        ]);
    }

    /** @test */
    public function user_can_edit_a_partner_within_search_query()
    {
        $this->loginAsUser();
        $partner = factory(Partner::class)->create(['name' => 'Testing 123']);

        $this->visitRoute('partners.index', ['q' => '123']);
        $this->click('edit-partner-'.$partner->id);
        $this->seeRouteIs('partners.index', ['action' => 'edit', 'id' => $partner->id, 'q' => '123']);

        $this->submitForm(__('partner.update'), [
            'name'        => 'Partner 1 name',
            'description' => 'Partner 1 description',
        ]);

        $this->seeRouteIs('partners.index', ['q' => '123']);

        $this->seeInDatabase('partners', [
            'name'        => 'Partner 1 name',
            'description' => 'Partner 1 description',
        ]);
    }

    /** @test */
    public function user_can_delete_a_partner()
    {
        $this->loginAsUser();
        $partner = factory(Partner::class)->create();
        factory(Partner::class)->create();

        $this->visitRoute('partners.index', ['action' => 'edit', 'id' => $partner->id]);
        $this->click('del-partner-'.$partner->id);
        $this->seeRouteIs('partners.index', ['action' => 'delete', 'id' => $partner->id]);

        $this->seeInDatabase('partners', [
            'id' => $partner->id,
        ]);

        $this->press(__('app.delete_confirm_button'));

        $this->dontSeeInDatabase('partners', [
            'id' => $partner->id,
        ]);
    }
}

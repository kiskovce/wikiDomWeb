<?php

namespace Tests\Feature;

use Tests\TestCase;

class WebsitePresentationTest extends TestCase
{
    public function test_home_pages_have_localized_offer_navigation_and_contact(): void
    {
        foreach (['/' => 'sk', '/sk' => 'sk', '/cs' => 'cs', '/en' => 'en'] as $path => $locale) {
            $response = $this->get($path);

            $response->assertOk()
                ->assertSee('lang="'.$locale.'"', false)
                ->assertSee(__('site.hero.title_html', [], $locale), false)
                ->assertSee(__('site.hero.lead', [], $locale))
                ->assertSee(__('site.experience.intro', [], $locale))
                ->assertSee(__('site.experience.price_note', [], $locale))
                ->assertSee(__('site.experience.technical_note', [], $locale))
                ->assertSee('class="mobile-menu"', false)
                ->assertSee('href="/glb-viewer"', false)
                ->assertSee('href="mailto:info@wikidom.sk"', false)
                ->assertSee('src="/images/wikihouse-assembly.jpg"', false)
                ->assertSee(__('site.hero.photo_alt', [], $locale))
                ->assertSee(__('site.hero.photo_credit', [], $locale))
                ->assertSee('Javier Leiva / Wikimedia Commons')
                ->assertSee('https://creativecommons.org/licenses/by/2.0/', false)
                ->assertDontSee('<canvas', false)
                ->assertDontSee('three.module.js')
                ->assertDontSee('site.experience.')
                ->assertDontSee('14 000 €')
                ->assertDontSee('REI 90');

            foreach (['content', 'ponuka', 'system', 'postup', 'faq', 'kontakt'] as $anchor) {
                $response->assertSee('id="'.$anchor.'"', false);
            }
        }
    }

    public function test_new_copy_has_matching_keys_in_all_supported_languages(): void
    {
        $slovak = require lang_path('sk/site.php');

        foreach (['cs', 'en'] as $locale) {
            $translation = require lang_path($locale.'/site.php');
            $this->assertSame(array_keys($slovak['experience']), array_keys($translation['experience']));
            $this->assertCount(4, $translation['experience']['benefits']);
        }
    }

    public function test_homepage_photo_is_available_locally(): void
    {
        $path = public_path('images/wikihouse-assembly.jpg');
        $this->assertFileExists($path);
        $image = getimagesize($path);
        $this->assertNotFalse($image);
        $this->assertSame(1280, $image[0]);
        $this->assertSame(1280, $image[1]);
        $this->assertSame('image/jpeg', $image['mime']);
    }

    public function test_configurator_preserves_viewer_hooks_and_explains_estimate(): void
    {
        $response = $this->get('/glb-viewer');

        $response->assertOk()
            ->assertSee('Orientačný súčet dielov')
            ->assertSee('Prvý odhad, nie cena dokončeného domu.')
            ->assertSee('Návrh sa po obnovení stránky neuchová.')
            ->assertSee('href="mailto:info@wikidom.sk"', false);

        foreach ([
            'viewerContent', 'canvas', 'canvas2d', 'modelName', 'busy',
            'notice', 'hint', 'emptyState', 'emptyText', 'endPickers',
            'objectsList', 'sequenceList', 'sumLength', 'sumArea',
            'sumCount', 'sumPrice', 'clearAssembly',
        ] as $id) {
            $response->assertSee('id="'.$id.'"', false);
        }
    }
}

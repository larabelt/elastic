<?php

use Belt\Core\Testing;

class WebSearchFunctionalTest extends Testing\BeltTestCase
{

    public function test()
    {
        $this->refreshDB();
        $this->actAsSuper();

        # index
        $response = $this->json('GET', '/search?include=pages');
        $response->assertStatus(200);
    }

}
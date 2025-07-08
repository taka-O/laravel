<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class FeatureBaseTestCase extends TestCase
{
        use DatabaseMigrations;
    
        public function setUp(): void
        {
            parent::setUp();
            
            Artisan::call('migrate:fresh --seed');
        }
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasTable('users')) {
    Schema::create('users', function(Blueprint $table) { 
        $table->id(); 
        $table->string('name')->nullable(); 
        $table->string('email')->nullable(); 
        $table->string('password')->nullable(); 
        $table->rememberToken(); 
        $table->timestamps(); 
    });
    echo "Table users created in central DB.\n";
} else {
    echo "Table users already exists.\n";
}

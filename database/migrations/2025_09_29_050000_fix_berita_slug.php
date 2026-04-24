<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Berita;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix berita without slug
        $beritas = Berita::whereNull('slug')->orWhere('slug', '')->get();

        foreach($beritas as $berita) {
            $slug = Str::slug($berita->judul);

            // Check if slug already exists
            $existingSlug = Berita::where('slug', $slug)->where('id', '!=', $berita->id)->first();
            if ($existingSlug) {
                $slug = $slug . '-' . $berita->id;
            }

            $berita->slug = $slug;
            $berita->save();

            echo "Updated berita ID {$berita->id} with slug: {$slug}\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};

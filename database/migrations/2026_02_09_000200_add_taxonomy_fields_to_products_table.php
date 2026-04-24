<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('taxonomy_group')->nullable()->after('category');
            $table->string('taxonomy_category')->nullable()->after('taxonomy_group');
            $table->string('taxonomy_subcategory')->nullable()->after('taxonomy_category');
        });

        $menUnique = [
            'shirts' => true,
            'cargos' => true,
            "cargo's" => true,
            'hoodies' => true,
            'caps' => true,
            'bags' => true,
            'watches' => true,
            'training' => true,
            'football' => true,
            'gym' => true,
        ];
        $womenUnique = [
            'dresses' => true,
            'tops' => true,
            'skirts' => true,
            'jackets' => true,
            'heels' => true,
            'handbags' => true,
            'jewelry' => true,
            'scarves' => true,
            'makeup' => true,
            'skincare' => true,
            'perfume' => true,
            'tracksuits' => true,
            'bike shoes' => true,
            'running shoes' => true,
            'beauty' => true,
            'bags & accessories' => true,
        ];
        $kidsUnique = [
            'baby' => true,
            'boys' => true,
            'girls' => true,
            'hats' => true,
            'socks' => true,
        ];

        $rows = DB::table('products')->select('id', 'category')->get();
        foreach ($rows as $row) {
            $raw = trim((string) ($row->category ?? ''));
            if ($raw === '') {
                continue;
            }

            $group = null;
            $category = null;
            $subcategory = null;

            if (stripos($raw, 'Men ') === 0) {
                $group = 'men';
                $rest = trim(substr($raw, 4));
                if ($rest !== '') {
                    $category = strtoupper($rest);
                }
            } elseif (stripos($raw, 'Women ') === 0) {
                $group = 'women';
                $rest = trim(substr($raw, 6));
                if ($rest !== '') {
                    $category = strtoupper($rest);
                }
            } elseif (stripos($raw, 'Kids ') === 0) {
                $group = 'kids';
                $rest = trim(substr($raw, 5));
                if ($rest !== '') {
                    $category = strtoupper($rest);
                }
            }

            $key = strtolower($raw);
            if ($group === null) {
                if (isset($womenUnique[$key])) {
                    $group = 'women';
                } elseif (isset($menUnique[$key])) {
                    $group = 'men';
                } elseif (isset($kidsUnique[$key])) {
                    $group = 'kids';
                }
            }

            $top = strtoupper($raw);
            if ($category === null) {
                if (in_array($top, ['CLOTHING', 'SHOES', 'ACCESSORIES', 'SPORT', 'BEAUTY', 'SALE'], true)) {
                    $category = $top;
                }
            }

            if ($subcategory === null) {
                $isAll = preg_match('/^all\s+/i', $raw) === 1;
                if (! $isAll && ! in_array($top, ['CLOTHING', 'SHOES', 'ACCESSORIES', 'SPORT', 'BEAUTY', 'SALE'], true)) {
                    $subcategory = $raw;
                }
            }

            if ($group !== null || $category !== null || $subcategory !== null) {
                DB::table('products')->where('id', $row->id)->update([
                    'taxonomy_group' => $group,
                    'taxonomy_category' => $category,
                    'taxonomy_subcategory' => $subcategory,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['taxonomy_group', 'taxonomy_category', 'taxonomy_subcategory']);
        });
    }
};

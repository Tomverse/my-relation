<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentTstmp = now();

        $products = DB::table('products')
            ->select('id')
            ->get();

        $shops = DB::table('shops')
            ->select('id')
            ->get();

        $shopMax = \min(5, \count($shops));
        $relatedShops = [
            \min(2, $shopMax),
            \min(3, $shopMax),
            \min(2, $shopMax),
            \min(1, $shopMax),
            \min(4, $shopMax),
        ];
        $productMax = \min(\count($relatedShops), \count($products));

        $data = [];
        for ($i = 0; $i < $productMax; $i++) {
            $relatedShop = $relatedShops[$i];
            for ($j = 0; $j < $relatedShop; $j++) {
                $data[] = [
                    'created_at' => $currentTstmp,
                    'updated_at' => $currentTstmp,
                    'product_id' => $products[$i]->id,
                    'shop_id' => $shops[($i + $j) % $shopMax]->id,
                ];
            }
        }

        DB::table('product_shop')->insert($data);
    }
}

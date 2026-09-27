<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect(['products.create', 'products.update', 'products.delete', 'orders.view'])
            ->map(fn ($permission) => Permission::findOrCreate($permission, 'web'));

        Role::findOrCreate('admin', 'web')->syncPermissions($permissions);
        Role::findOrCreate('vendor', 'web')->syncPermissions($permissions->whereIn('name', [
            'products.create', 'products.update', 'products.delete', 'orders.view',
        ]));
        Role::findOrCreate('customer', 'web');

        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            ['name' => 'Fieldwork Studio', 'password' => Hash::make('password')],
        );
        $seller->assignRole('vendor');

        $vendor = Vendor::firstOrCreate(
            ['user_id' => $seller->id],
            ['business_name' => 'Fieldwork Studio', 'slug' => 'fieldwork-studio', 'description' => 'Useful goods, made to last.'],
        );

        $catalog = [
            ['name' => 'Everyday Carry Tote', 'slug' => 'everyday-carry-tote', 'category' => 'Accessories', 'price' => 38, 'stock' => 24, 'image' => 'photo-1590874103328-eac38a683ce7'],
            ['name' => 'Studio Ceramic Cup', 'slug' => 'studio-ceramic-cup', 'category' => 'Home', 'price' => 24, 'stock' => 18, 'image' => 'photo-1514228742587-6b1558fcca3d'],
            ['name' => 'Market Day Shirt', 'slug' => 'market-day-shirt', 'category' => 'Apparel', 'price' => 64, 'stock' => 12, 'image' => 'photo-1521572163474-6864f9cf17ab'],
            ['name' => 'Trail Notes Journal', 'slug' => 'trail-notes-journal', 'category' => 'Stationery', 'price' => 18, 'stock' => 40, 'image' => 'photo-1544947950-fa07a98d237f'],
        ];

        foreach ($catalog as $item) {
            $categorySlug = str($item['category'])->slug();
            $category = Category::firstOrCreate(
                ['slug' => $categorySlug],
                ['name' => $item['category'], 'is_active' => true],
            );

            Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'vendor_id' => $vendor->id,
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'description' => 'Thoughtfully made by an independent marketplace seller.',
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'status' => 'active',
                    'image_url' => 'https://images.unsplash.com/'.$item['image'].'?auto=format&fit=crop&w=900&q=80',
                ],
            );
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StoreVendorRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class VendorController extends Controller
{
    public function applyForm()
    {
        return view('vendor-apply');
    }

    public function apply(StoreVendorRequest $request)
    {
        $user = $request->user();
        abort_if($user->vendor, 409, 'A vendor profile already exists.');

        $slug = Str::slug($request->validated('business_name'));
        $base = $slug;
        $suffix = 2;
        while (Vendor::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        $user->vendor()->create([
            ...$request->validated(),
            'slug' => $slug,
            'status' => 'active',
        ]);
        $this->ensureVendorAccess($user);

        return redirect()->route('vendor.dashboard')->with('status', 'Your shop is ready.');
    }

    public function dashboard()
    {
        $vendor = auth()->user()->vendor;
        if (! $vendor) {
            return redirect()->route('vendor.apply')->with('status', 'Create your shop profile to manage products.');
        }

        $this->ensureVendorAccess(auth()->user());

        return view('vendor-dashboard', [
            'vendor' => $vendor,
            'products' => $vendor->products()->with('category')->latest()->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'orderItems' => $vendor->load(['products.orderItems.order.user'])->products
                ->flatMap->orderItems
                ->sortByDesc('created_at')
                ->take(8),
        ]);
    }

    public function storeProduct(StoreProductRequest $request)
    {
        $vendor = $request->user()->vendor;
        abort_unless($vendor && $vendor->status === 'active', 403);

        $data = $request->safe()->except('image');
        $slugBase = Str::slug($data['name']) ?: 'product';
        $slug = $slugBase;
        $suffix = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$suffix++;
        }

        $data['slug'] = $slug;
        $product = $vendor->products()->create($data);

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('products.show', $product)->with('status', 'Product published in your shop.');
    }

    public function deleteProduct(Product $product)
    {
        abort_unless(auth()->user()->can('products.delete') && auth()->user()->vendor?->id === $product->vendor_id, 403);
        $product->delete();

        return redirect()->route('vendor.dashboard')->with('status', 'Product removed.');
    }

    private function ensureVendorAccess(User $user): void
    {
        $role = Role::findOrCreate('vendor', 'web');
        $permissions = collect(['products.create', 'products.update', 'products.delete', 'orders.view'])
            ->map(fn ($permission) => Permission::findOrCreate($permission, 'web'));

        $role->givePermissionTo($permissions->all());
        $user->assignRole($role);
    }
}

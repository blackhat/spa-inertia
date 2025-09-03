## ทวน การแสดงผลหน้า Product
1.    Route::resource('products', ProductController::class); 
2.    php artisan route:list --path=products
3.     
ProductController.php
```php
public function index()
    {
        return inertia('Product/Index');
    }
```
4. Product/Index.vue
```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Product" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Product List
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        This is Product List.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
```
5. 
```vue
    <NavLink
        :href="route('products.index')"
        :active="route().current('products.*')"
    >
        Product
    </NavLink>
```

ทำ Resource
```bash
php artisan make:resource CategoryResource
```
```php
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
```
```bash
php artisan make:resource ProductResource
```

```php
        //ยังไม่ทำ mutator, accessor
        $price = $this->price / 100;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'brand' => $this->brand,
            'price' => $price,
            'price_formatted' => '$'.$price,
            'weight' => $this->weight,
            'description' => $this->description,
        ];
```

AppServiceProvider.php
```php
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        JsonResource::withoutWrapping();
    }
```
ProductController
```php
    // $products = Product::query()
    //     ->whereBelongsTo($request->user())
    //     ->with(['category:id,name'])
    //     ->paginate(10)
    //     ->withQueryString();
    $products = auth()->user()->products()->with(['category:id,name'])->get();
```


```php
        $products = auth()->user()->products()->with(['category:id,name'])->latest()->paginate(5);

```
```vue
<script setup>
defineProps({
    products: {
        type: Object,
        required: true
    }
})
</script>

<template>
<tr v-for="product in products.data" :key="product.id">
</template>
```
เรียกใช้ Pagination.vue
```php
import Pagination from '@/Components/Pagination.vue'

<th>No</th>
<tr v-for="(product, index) in products.data" :key="products.id" class="bg-white border-b hover:bg-gray-50">

<td>{{ products.meta.from + index }}</td>

<Pagination :meta="products.meta" />
```

@/Components/Pagination.vue

```vue
<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    meta: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <nav class="flex flex-wrap items-center justify-between px-4 py-2 flex-column md:flex-row" aria-label="Table navigation">
        <span class="block w-full mb-4 text-sm font-normal text-gray-700 md:mb-0 md:inline md:w-auto">Showing 
            <span class="font-semibold text-gray-700">{{ meta.from }}-{{ meta.to }}</span> of 
            <span class="font-semibold text-gray-700">{{ meta.total }}</span>
        </span>
        <ul class="inline-flex h-8 -space-x-px text-sm rtl:space-x-reverse">
            <li v-for="(link, index) in meta.links" :key="index">
                <Link 
                    preserve-scroll
                    :href="link.url ?? ''" 
                    class="flex items-center justify-center h-8 px-3 ms-0"
                    :class="{
                        'leading-tight text-gray-500 hover:text-gray-700': !link.active,
                        'text-blue-600 hover:text-blue-700': link.active,
                        'text-stone-400 hover:text-stone-400': !link.url
                    }"
                    v-html="link.label"
                    ></Link>
            </li>
        </ul>
    </nav>
</template>
```



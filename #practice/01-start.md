```bash
laravel new spa-inertia

composer require laravel/breeze --dev
php artisan breeze:install


npm i -D vite@^6 @vitejs/plugin-vue@^5 laravel-vite-plugin@^1

php artisan make:model Category -rf

php artisan make:model
Product no seeder
```

```bash
php artisan make:migration create_products_table
php artisan make:migration add_price_to_products_table --table=products
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->integer('stock')->after('price'); // ตัวอย่างเพิ่มฟิลด์
    });
}
```
หมายเหตุ: ถ้าจะ rename/drop column บางกรณีอาจต้องติดตั้ง doctrine/dbal
```bash
composer require --dev doctrine/dbal
```


 seeder
 ```php
         $categories = Category::factory(5)->create();
        // User::factory(5)
        //     ->has(
        //         Product::factory(10)->state(function () use ($categories) {
        //             return ['category_id' => $categories->random()->id];
        //         })
        //     )
        //     ->create();
 

        User::factory(5)
        ->has(Product::factory(10)->state(
            fn () => ['category_id' => $categories->random()->id]
        ))
        ->create();
  ```

 
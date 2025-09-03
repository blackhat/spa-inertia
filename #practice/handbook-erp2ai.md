# ERP2AI Handbook — Best Practices (Laravel 12 + Inertia 2 + Vue 3)

> โฟกัส “หลักการ+เช็กลิสต์พร้อมใช้” ไม่พาลัด scaffold

---

## 0) โครงสร้างโปรเจกต์ (ย่อ)
```
app/
  Models/
    Production/Item.php
    Catalog/{Product.php, Category.php}
  Http/
    Controllers/{Domain}/*.php
    Requests/{Domain}/*.php
  Services/{Domain}/*.php
  Actions/{Domain}/*.php
database/
  migrations/*.php
  seeders/*.php
  factories/*.php
resources/js/
  pages/{Domain}/*.vue
  components/*.vue
  composables/*.ts
```
- แยกโค้ด **ตามโดเมน** (กติกา ERP2AI)
- ใช้ **FormRequest**, **Service/Action** แยก business logic ออก

---

## 1) Schema Design (สรุปเข้ม)
- ตารางใช้ **พหูพจน์**, PK = `id()`, FK = `foreignId`
- **ลำดับ migration**: Parent ? Child ? Pivot
- เงิน: `unsignedBigInteger` (สตางค์) หรือ `decimal(12,2)` (เลือกแนวทางเดียวทั้งระบบ)
- น้ำหนักโลหะ: `decimal(8,3)` (ทองละเอียดได้)
- ใส่ `->comment()` ทุกคอลัมน์/ตาราง
- Index:
  - `unique()` กับ code/slug/email
  - `index()` กับคอลัมน์ที่ query บ่อย (status, date, fk)
- Soft delete เมื่อ “ต้องเก็บประวัติ”: `$table->softDeletes()`
- ตัวอย่าง
```php
Schema::create('categories', function (Blueprint $t) {
  $t->id();
  $t->string('name')->index()->comment('ชื่อหมวด');
  $t->timestamps();
  $t->comment('หมวดสินค้า');
});

Schema::create('products', function (Blueprint $t) {
  $t->id()->comment('PK');
  $t->string('name')->index()->comment('ชื่อสินค้า');
  $t->foreignId('user_id')->constrained()->comment('เจ้าของข้อมูล');
  $t->foreignId('category_id')->constrained()->comment('หมวด');
  $t->string('brand')->nullable()->comment('แบรนด์');
  $t->unsignedBigInteger('price')->comment('ราคา(สตางค์)');
  $t->decimal('weight', 8, 3)->default(0)->comment('กรัม');
  $t->text('description')->nullable()->comment('คำอธิบาย');
  $t->timestamps();
  $t->softDeletes();
  $t->comment('ตารางสินค้า');
});
```

**Common Mistakes**
- สลับลำดับ migration ? FK พัง
- ชนิดคอลัมน์ของ FK ไม่ตรงกับ PK
- เก็บเงินเป็น `float` ? คลาดเคลื่อน

---

## 2) Seeder & Factory (สร้างข้อมูลจำลอง “มี FK ถูกลำดับ”)
**หลัก**
- Seed “Parent ก่อน Child”
- ใช้ `state()` ผูก FK แบบสุ่มจากชุดที่มีอยู่
- ใส่ปริมาณพอทดสอบ pagination/filter ได้ (เช่น 100–500 แถว)

**ตัวอย่าง**
```php
// CategoryFactory
return ['name' => fake()->unique()->word()];

// ProductFactory
return [
  'name' => fake()->unique()->sentence(3),
  'brand' => fake()->company(),
  'price' => fake()->numberBetween(1000, 500000), // 10–5,000 บาท (สตางค์)
  'weight' => fake()->randomFloat(3, 0, 50),      // 0–50 กรัม
  'description' => fake()->optional()->text(200),
];

// DatabaseSeeder
$categories = \App\Models\Catalog\Category::factory(10)->create();

\App\Models\User::factory(5)
  ->has(
    \App\Models\Catalog\Product::factory(50)->state(fn() => [
      'category_id' => $categories->random()->id,
    ])
  )->create();
```

**Checklist**
- [ ] ใช้ `unique()` กับค่าที่ต้องไม่ซ้ำ
- [ ] ไม่ seed ข้อมูล “สกปรก” ที่ละเมิด constraint
- [ ] `migrate:fresh --seed` ต้องผ่านรวดเดียว

---

## 3) Request & Validation (FormRequest เป็นมาตรฐาน)
**หลัก**
- ไม่ยัด validation ใน Controller
- ใช้ rule สั้น กระชับ, สอดคล้อง schema
- ระวัง **การอัปเดต** (unique:ignore)

**ตัวอย่าง**
```php
class StoreProductRequest extends FormRequest {
  public function rules(): array {
    return [
      'name' => ['required','string','max:255'],
      'brand' => ['nullable','string','max:255'],
      'category_id' => ['required','exists:categories,id'],
      'price' => ['required','integer','min:0'],       // สตางค์
      'weight' => ['nullable','numeric','min:0'],      // กรัม (ทศนิยมได้)
      'description' => ['nullable','string','max:5000'],
    ];
  }
}
```
**Checklist**
- [ ] ใช้ `FormRequest` เสมอ
- [ ] ตรวจ `exists` กับ FK ทุกตัว
- [ ] แยก rule “create vs update” ให้ชัด

---

## 4) Relation & Query (Eloquent อย่างมีสติ)
**Model**
```php
class Product extends Model {
  use HasFactory;
  protected $fillable = ['name','brand','price','weight','description','category_id','user_id'];

  public function category() { return $this->belongsTo(Category::class); }
  public function user() { return $this->belongsTo(User::class); }
}
class Category extends Model {
  public function products() { return $this->hasMany(Product::class); }
}
```

**Query Best Practices**
- ใช้ `with()` ป้องกัน N+1
- จำกัดคอลัมน์ด้วย `select()` เมื่อ data กว้าง
- แยก **scope** สำหรับ filter/sort ให้ reuse ได้

```php
// In Product model
public function scopeFilter($q, array $f) {
  $q->when($f['search'] ?? null, fn($q,$s) =>
    $q->where(fn($x) => $x->where('name','like',"%$s%")
                          ->orWhere('brand','like',"%$s%")));
  $q->when($f['category_id'] ?? null, fn($q,$cid) => $q->where('category_id',$cid));
}

public function scopeSort($q, $col='created_at', $dir='desc') {
  $allowed = ['name','price','created_at'];
  $col = in_array($col,$allowed) ? $col : 'created_at';
  $dir = $dir === 'asc' ? 'asc' : 'desc';
  return $q->orderBy($col, $dir);
}
```

**ตัวอย่าง Controller (Index)**
```php
$query = Product::query()
  ->with(['category:id,name','user:id,name'])
  ->filter(request()->only('search','category_id'))
  ->sort(request('sort'), request('dir'));

$products = $query->paginate(15)->withQueryString();
```

---

## 5) Migration Tricks & Refactor
- เปลี่ยนคอลัมน์: `composer require doctrine/dbal` ? ใช้ `->change()`
- เพิ่ม FK ภายหลัง (หากลำดับยาก):
  - migration A: สร้างคอลัมน์ธรรมดา
  - migration B: เพิ่ม constraint
- Dump schema ลดไฟล์เก่า: `php artisan schema:dump`
- ใช้ `enum`/`string` ชัดเจนกับสถานะงาน, และมี mapping ในโค้ด

**ตัวอย่างเพิ่ม FK ภายหลัง**
```php
Schema::table('products', function (Blueprint $t) {
  $t->foreign('category_id')->references('id')->on('categories');
});
```

---

## 6) Inertia + Vue 3 (ตาราง/ค้นหา/จัดหน้า)
**หลัก**
- ส่ง `props` แบบเรียบง่าย: `data`, `meta`, `links`
- ค้นหา/จัดหน้า ควรใช้ `withQueryString()` ฝั่ง Laravel
- ปุ่ม “ล้างตัวกรอง” เคลียร์ query ทุกตัว

**ตัวอย่าง ส่งข้อมูลจาก Controller**
```php
return Inertia::render('Catalog/Products/Index', [
  'products' => $products->through(fn($p) => [
    'id' => $p->id,
    'name' => $p->name,
    'category' => $p->category?->name,
    'brand' => $p->brand,
    'price_formatted' => number_format($p->price/100, 2),
    'weight' => (float) $p->weight,
  ]),
  'filters' => request()->only('search','category_id','sort','dir'),
  'categories' => Category::select('id','name')->orderBy('name')->get(),
]);
```

**Vue Checklist**
- [ ] รับ `filters` ? bind กับ input
- [ ] ใช้ `router.get()` พร้อม `{ preserveState: true, replace: true }`
- [ ] Debounce การค้นหา

---

## 7) Transaction & Consistency
- ธุรกรรมที่แตะหลายตาราง ? ใช้ `DB::transaction()` เสมอ
```php
DB::transaction(function () use ($dto) {
  $product = Product::create($dto->toArray());
  // insert related rows...
});
```
- หลีกเลี่ยง side-effect ใน Controller ? ไปไว้ Service/Action

---

## 8) Performance & Scale
- ตรวจ N+1 ด้วย `->with()` และ Laravel Debugbar (เฉพาะ dev)
- Index ให้พอเหมาะกับ query จริง
- ใช้ `chunk()`/`cursor()` กับงาน batch
- แยก read/write ถ้าโตมาก (อนาคต)

---

## 9) Logging & Audit
- เก็บ `created_by`, `updated_by` ถ้าต้อง trace ผู้ใช้
- ใช้ Observer หรือ Model event เพื่อ set ค่าอัตโนมัติ
- พิจารณาแยกตาราง audit log เมื่อต้องการ history ละเอียด

---

## 10) Do & Don’t (สรุป)
**Do**
- ออกแบบ ERD ก่อนเขียน migration
- Parent ? Child ? Pivot
- FormRequest ทุกจุดรับ input
- ใส่ comment/indices อย่างตั้งใจ
- มี seed ข้อมูลพอทดสอบ

**Don’t**
- ยัด validation ใน Controller
- เก็บเงินเป็น float
- ลืม `exists` กับ FK
- โหลด relation แบบ N+1
- ปล่อยให้ migration แตกแขนงโดยไม่จัดระเบียบ

---
**Command ที่ใช้บ่อย**
```
php artisan make:model Catalog/Category -mfs
php artisan make:model Catalog/Product -mfs
php artisan make:request Catalog/StoreProductRequest
php artisan migrate:fresh --seed
php artisan schema:dump
```

> อ่านจบ = ใช้ได้จริงทั้งทีม (มาตรฐานเดียวกัน) — นี่คือฐานสำหรับ ERP2AI

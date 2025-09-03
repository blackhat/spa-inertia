# ERP2AI Handbook � Best Practices (Laravel 12 + Inertia 2 + Vue 3)

> ⿡�� ���ѡ���+����ʵ������� �����Ѵ scaffold

---

## 0) �ç���ҧ��ਡ�� (���)
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
- �¡�� **�������** (��ԡ� ERP2AI)
- �� **FormRequest**, **Service/Action** �¡ business logic �͡

---

## 1) Schema Design (��ػ���)
- ���ҧ�� **��پ���**, PK = `id()`, FK = `foreignId`
- **�ӴѺ migration**: Parent ? Child ? Pivot
- �Թ: `unsignedBigInteger` (ʵҧ��) ���� `decimal(12,2)` (���͡�Ƿҧ���Ƿ���к�)
- ���˹ѡ����: `decimal(8,3)` (�ͧ�����´��)
- ��� `->comment()` �ء�������/���ҧ
- Index:
  - `unique()` �Ѻ code/slug/email
  - `index()` �Ѻ��������� query ���� (status, date, fk)
- Soft delete ����� ���ͧ�纻���ѵԔ: `$table->softDeletes()`
- ������ҧ
```php
Schema::create('categories', function (Blueprint $t) {
  $t->id();
  $t->string('name')->index()->comment('������Ǵ');
  $t->timestamps();
  $t->comment('��Ǵ�Թ���');
});

Schema::create('products', function (Blueprint $t) {
  $t->id()->comment('PK');
  $t->string('name')->index()->comment('�����Թ���');
  $t->foreignId('user_id')->constrained()->comment('��Ңͧ������');
  $t->foreignId('category_id')->constrained()->comment('��Ǵ');
  $t->string('brand')->nullable()->comment('�ù��');
  $t->unsignedBigInteger('price')->comment('�Ҥ�(ʵҧ��)');
  $t->decimal('weight', 8, 3)->default(0)->comment('����');
  $t->text('description')->nullable()->comment('��͸Ժ��');
  $t->timestamps();
  $t->softDeletes();
  $t->comment('���ҧ�Թ���');
});
```

**Common Mistakes**
- ��Ѻ�ӴѺ migration ? FK �ѧ
- ��Դ�������ͧ FK ���ç�Ѻ PK
- ���Թ�� `float` ? ��Ҵ����͹

---

## 2) Seeder & Factory (���ҧ�����Ũ��ͧ ��� FK �١�ӴѺ�)
**��ѡ**
- Seed �Parent ��͹ Child�
- �� `state()` �١ FK Ẻ�����ҡ�ش���������
- ������ҳ�ͷ��ͺ pagination/filter �� (�� 100�500 ��)

**������ҧ**
```php
// CategoryFactory
return ['name' => fake()->unique()->word()];

// ProductFactory
return [
  'name' => fake()->unique()->sentence(3),
  'brand' => fake()->company(),
  'price' => fake()->numberBetween(1000, 500000), // 10�5,000 �ҷ (ʵҧ��)
  'weight' => fake()->randomFloat(3, 0, 50),      // 0�50 ����
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
- [ ] �� `unique()` �Ѻ��ҷ���ͧ�����
- [ ] ��� seed ������ �ʡ�á� �������Դ constraint
- [ ] `migrate:fresh --seed` ��ͧ��ҹ�Ǵ����

---

## 3) Request & Validation (FormRequest ���ҵðҹ)
**��ѡ**
- ����Ѵ validation � Controller
- �� rule ��� ��ЪѺ, �ʹ���ͧ schema
- ���ѧ **����ѻവ** (unique:ignore)

**������ҧ**
```php
class StoreProductRequest extends FormRequest {
  public function rules(): array {
    return [
      'name' => ['required','string','max:255'],
      'brand' => ['nullable','string','max:255'],
      'category_id' => ['required','exists:categories,id'],
      'price' => ['required','integer','min:0'],       // ʵҧ��
      'weight' => ['nullable','numeric','min:0'],      // ���� (�ȹ�����)
      'description' => ['nullable','string','max:5000'],
    ];
  }
}
```
**Checklist**
- [ ] �� `FormRequest` ����
- [ ] ��Ǩ `exists` �Ѻ FK �ء���
- [ ] �¡ rule �create vs update� ���Ѵ

---

## 4) Relation & Query (Eloquent ���ҧ��ʵ�)
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
- �� `with()` ��ͧ�ѹ N+1
- �ӡѴ���������� `select()` ����� data ���ҧ
- �¡ **scope** ����Ѻ filter/sort ��� reuse ��

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

**������ҧ Controller (Index)**
```php
$query = Product::query()
  ->with(['category:id,name','user:id,name'])
  ->filter(request()->only('search','category_id'))
  ->sort(request('sort'), request('dir'));

$products = $query->paginate(15)->withQueryString();
```

---

## 5) Migration Tricks & Refactor
- ����¹�������: `composer require doctrine/dbal` ? �� `->change()`
- ���� FK �����ѧ (�ҡ�ӴѺ�ҡ):
  - migration A: ���ҧ������������
  - migration B: ���� constraint
- Dump schema Ŵ������: `php artisan schema:dump`
- �� `enum`/`string` �Ѵਹ�ѺʶҹЧҹ, ����� mapping ���

**������ҧ���� FK �����ѧ**
```php
Schema::table('products', function (Blueprint $t) {
  $t->foreign('category_id')->references('id')->on('categories');
});
```

---

## 6) Inertia + Vue 3 (���ҧ/����/�Ѵ˹��)
**��ѡ**
- �� `props` Ẻ���º����: `data`, `meta`, `links`
- ����/�Ѵ˹�� ����� `withQueryString()` ��� Laravel
- ���� ���ҧ��ǡ�ͧ� ������ query �ء���

**������ҧ �觢����Ũҡ Controller**
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
- [ ] �Ѻ `filters` ? bind �Ѻ input
- [ ] �� `router.get()` ����� `{ preserveState: true, replace: true }`
- [ ] Debounce ��ä���

---

## 7) Transaction & Consistency
- ��á�����������µ��ҧ ? �� `DB::transaction()` ����
```php
DB::transaction(function () use ($dto) {
  $product = Product::create($dto->toArray());
  // insert related rows...
});
```
- ��ա����§ side-effect � Controller ? ���� Service/Action

---

## 8) Performance & Scale
- ��Ǩ N+1 ���� `->with()` ��� Laravel Debugbar (੾�� dev)
- Index ��������СѺ query ��ԧ
- �� `chunk()`/`cursor()` �Ѻ�ҹ batch
- �¡ read/write �����ҡ (͹Ҥ�)

---

## 9) Logging & Audit
- �� `created_by`, `updated_by` ��ҵ�ͧ trace �����
- �� Observer ���� Model event ���� set ����ѵ��ѵ�
- �Ԩ�ó��¡���ҧ audit log ����͵�ͧ��� history �����´

---

## 10) Do & Don�t (��ػ)
**Do**
- �͡Ẻ ERD ��͹��¹ migration
- Parent ? Child ? Pivot
- FormRequest �ء�ش�Ѻ input
- ��� comment/indices ���ҧ����
- �� seed �����žͷ��ͺ

**Don�t**
- �Ѵ validation � Controller
- ���Թ�� float
- ��� `exists` �Ѻ FK
- ��Ŵ relation Ẻ N+1
- �������� migration ᵡᢹ������Ѵ����º

---
**Command ��������**
```
php artisan make:model Catalog/Category -mfs
php artisan make:model Catalog/Product -mfs
php artisan make:request Catalog/StoreProductRequest
php artisan migrate:fresh --seed
php artisan schema:dump
```

> ��ҹ�� = �����ԧ��駷�� (�ҵðҹ���ǡѹ) � ����Ͱҹ����Ѻ ERP2AI

# ERP2AI Handbook � Accessors & Mutators (Laravel 12)

> ����� Model �� ���鹡�ҧ�Ѩ����Д �Ѵ��� data format �͹���-�͡ DB ������з� logic ������

---

## 1) ��ѡ���
- **Mutator (setXxxAttribute)** ? �Ѵ��ä�ҡ�͹ **�ѹ�֡ŧ DB**  
- **Accessor (getXxxAttribute)** ? �Ѵ��ä�ҡ�͹ **���͡�ҡ Model**

**��ʹ�**
- ��¹ logic ��������ǔ ? ����ء��� (Factory, Controller, API, Vue ��ҹ Inertia)
- DB �红����ŴԺẺ������� ���� app ���¡���дǡ

---

## 2) ������ҧ: �Ҥ� (����ʵҧ�� �����¡�繺ҷ)
```php
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','brand','weight','description','category_id','user_id'];

    // Mutator ? save ��ʵҧ��
    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = (int) round($value * 100);
    }

    // Accessor ? read �繺ҷ
    public function getPriceAttribute($value): float
    {
        return $value / 100;
    }

    // Accessor ? �Ҥ� format string
    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price, 2);
    }
}
```

**���Ѿ��**
```php
Product::create(['name'=>'Ring','price'=>199.99]);
// DB ��: 19999
$product->price;            // 199.99
$product->price_formatted;  // "199.99"
```

---

## 3) ������ҧ: �ٻ�Ҿ (������� �����¡ URL)
```php
class Product extends Model
{
    protected $fillable = ['name','image'];

    // Mutator ? ��੾�Ъ������
    public function setImageAttribute($value): void
    {
        // ���������ѻ��Ŵ
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $path = $value->store('products','public');
            $this->attributes['image'] = $path;
        } else {
            $this->attributes['image'] = $value;
        }
    }

    // Accessor ? �׹��� URL �������ҹ
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : asset('images/no-image.png');
    }
}
```

**���Ѿ��**
```php
$product = Product::find(1);
$product->image;     // "products/abc123.jpg"
$product->image_url; // "https://yourapp.test/storage/products/abc123.jpg"
```

---

## 4) ������ҧ: ʶҹ� (���繵���Ţ �����¡�繢�ͤ���)
```php
class Order extends Model
{
    protected $fillable = ['status'];

    const STATUS_PENDING   = 0;
    const STATUS_APPROVED  = 1;
    const STATUS_REJECTED  = 2;

    // Accessor ? �ŧ�� label
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }
}
```

**���Ѿ��**
```php
$order->status;       // 1
$order->status_label; // "Approved"
```

---

## 5) Checklist ����� Accessor/Mutator
- [ ] ������� �DB ��Ẻ˹�� ������ҹ��ͧ����աẺ�
- [ ] ��駪��� attribute ��� **���ͤ�������** (`price_formatted`, `image_url`)
- [ ] ����Ѵ logic ˹ѡ � � accessor (����� format �� � ��ҹ��)
- [ ] ��� type �ͧ��ҷ�� return (`float`, `string`) ���� consistency
- [ ] ���ͺ���� seeder/factory ���������� logic ���ѧ

---

## 6) Common Mistakes
- ? ��¹ logic format � Controller ? ��ӫ�͹
- ? ��� Vue/Frontend 仨Ѵ����ͧ ? ����ʹ���ͧ����к�
- ? ��� type cast ? �� string/float ��Ѻ�ѹ
- ? ��� logic business (�� �ӹǳ����) ���� accessor ? ����� query ˹ѡ

---

## 7) Best Practice (ERP Context)
- **�Թ** ? DB �� �ʵҧ�� (int), accessor �׹ ��ҷ� (float/string)
- **�ٻ�Ҿ/���** ? DB �� path, accessor �׹ URL
- **ʶҹ�** ? DB �� code/enum, accessor �׹ label (����/����ҹ)
- **�ѹ���** ? DB �� timestamp, accessor �׹ format ��§�� (�� `d/m/Y`)

---

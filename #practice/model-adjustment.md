## Model Adjustment
# Product.php
```php
use Illuminate\Database\Eloquent\Casts\Attribute;

    protected function price(): Attribute
    {
        return Attribute::make(
            set: fn (int $value) => $value * 100,
            get: fn (int $value) => $value / 100
        );
    }
```
# Cheat Sheet: Laravel Resource & Relation

## 1) One-to-One / Many-to-One (belongsTo, hasOne)
```php
'category' => CategoryResource::make($this->whenLoaded('category')),
```
- �� `Resource::make(...)`
- ��� relation �ѧ����� `with('category')` ? field ���� `null`

---

## 2) One-to-Many / Many-to-Many (hasMany, belongsToMany)
```php
'products' => ProductResource::collection($this->whenLoaded('products')),
```
- �� `Resource::collection(...)`
- �׹ array �ͧ resource

---

## 3) �֧ field ���Ǩҡ relation
```php
'category_name' => $this->whenLoaded('category', fn () => $this->category->name),
```
- �׹��� field ���� �� `name`
- �����ҷ�������ҡ�� resource ��͹

---

## 4) ��͹ relation ����������Ŵ
```php
'category' => $this->when(
    $this->relationLoaded('category'),
    fn () => new CategoryResource($this->category)
),
```
- �� `relationLoaded()` ᷹ `whenLoaded()` �����ҡ�Ǻ����Ѵਹ

---

## 5) Tips
- �� `with(['category','user'])` � Controller ���ͻ�ͧ�ѹ N+1
- �� `make()` ������� object ����
- �� `collection()` ������� array/collection
- �� `whenLoaded()` �������������� relation �١��Ŵ�����ѧ
- �� `when()` + `relationLoaded()` �������ҡ���͹������´����

---
# Laravel Resource � �óյ�ͧ�� `collection()` ��е��ҧ Mapping Relation

## 1) �óշ���ͧ�� `Resource::collection()`

������� relation �׹������� **���� record** �����ҵ�ͧ����觢������١������价�� frontend ��:

- **Category ? Products (hasMany)**  
  ```php
  'products' => ProductResource::collection($this->whenLoaded('products')),
  ```
  �ʴ��Թ��ҷ��������Ǵ����

- **Order ? Items (hasMany)**  
  ```php
  'items' => OrderItemResource::collection($this->whenLoaded('items')),
  ```
  �ʴ��Թ�������觫���

- **User ? Roles (belongsToMany)**  
  ```php
  'roles' => RoleResource::collection($this->whenLoaded('roles')),
  ```
  �ʴ� role �������ͧ user

- **Post ? Comments (morphMany)**  
  ```php
  'comments' => CommentResource::collection($this->whenLoaded('comments')),
  ```
  �ʴ���������������ͧ�ʵ�

---

## 2) ���ҧ Mapping Relation ? ����� Resource

| ������ Relation          | ������ҧ Model ? Relation      | �� Resource Ẻ�˹ |
|---------------------------|---------------------------------|----------------------|
| `belongsTo`               | Product ? Category              | `Resource::make()`   |
| `hasOne`                  | User ? Profile                  | `Resource::make()`   |
| `hasMany`                 | Category ? Products             | `Resource::collection()` |
| `belongsToMany`           | User ? Roles                    | `Resource::collection()` |
| `morphOne`                | Post ? Image                    | `Resource::make()`   |
| `morphMany`               | Post ? Comments                 | `Resource::collection()` |
| `morphTo`                 | Comment ? commentable (Post/User)| `Resource::make()`   |
| `morphToMany`             | Tag ? Posts, Videos             | `Resource::collection()` |

---

## 3) Tips
- �� `make()` ? ����� relation �׹ object ���� (one-to-one, many-to-one)  
- �� `collection()` ? ����� relation �׹���� object (one-to-many, many-to-many)  
- �� `whenLoaded('relation')` ? ��ͧ�ѹ N+1 �������� relation �������� eager load  
- �� `when()` + `relationLoaded()` ? �����ҡ�Ǻ��������´��� field ������������  

---

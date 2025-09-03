# Search Input Patterns (Vue 3 + Inertia)

## 1) Realtime + Debounce (แนะนำบ่อยสุด)
ใช้ค้นหาทุกครั้งที่พิมพ์ แต่หน่วง 300ms เพื่อลด request ถี่ ๆ

```ts
import { debounce } from 'lodash-es'
import { router } from '@inertiajs/vue3'

const doSearch = debounce((value: string) => {
  router.get(route('products.index'), { search: value }, {
    only: ['products'],
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}, 300)

const handleInput = (e: Event) => {
  const value = (e.target as HTMLInputElement).value
  doSearch(value)
}
```

```vue
<input
  type="text"
  placeholder="Search products…"
  @input="handleInput"
/>
```

---

## 2) Enter-to-search (เบาเครื่อง, ชัวร์สุด)
ใช้ค้นหาเฉพาะตอนกด Enter

```ts
import { router } from '@inertiajs/vue3'

const handleEnter = (e: KeyboardEvent) => {
  const value = (e.target as HTMLInputElement).value
  router.get(route('products.index'), { search: value }, {
    only: ['products'],
    preserveState: true,
    preserveScroll: true,
    replace: true,
    onSuccess: () => (e.target as HTMLInputElement).focus(),
  })
}
```

```vue
<input
  type="text"
  placeholder="Search products…"
  @keyup.enter="handleEnter"
/>
```

---

## 3) Hybrid (≥3 ตัวอักษร realtime, <3 ต้อง Enter)
ผสมสองแนวทาง: ถ้าพิมพ์สั้น (<3 ตัว) ต้องกด Enter, ถ้ายาวกว่า 3 ตัว realtime + debounce

```ts
import { debounce } from 'lodash-es'
import { router } from '@inertiajs/vue3'

const doSearch = debounce((v: string) => {
  router.get(route('products.index'), { search: v }, {
    only: ['products'],
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}, 300)

const handleInputHybrid = (e: Event) => {
  const v = (e.target as HTMLInputElement).value
  if (v.length >= 3) doSearch(v)
}

const handleEnterHybrid = (e: KeyboardEvent) => {
  const v = (e.target as HTMLInputElement).value
  router.get(route('products.index'), { search: v }, {
    only: ['products'],
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}
```

```vue
<input
  type="text"
  placeholder="Search… (Enter for short)"
  @input="handleInputHybrid"
  @keyup.enter="handleEnterHybrid"
/>
```

---

## ทริคให้โปรขึ้น
- ใส่ `only: ['products']` ลด payload หน้า Inertia
- ใช้ `replace: true` กัน history ยาวเวลาพิมพ์รัว ๆ
- ใส่ loading state:
  ```ts
  let loading = $ref(false)
  router.get(..., {
    onStart: () => loading = true,
    onFinish: () => loading = false,
  })
  ```
- Inertia จะ cancel request ก่อนหน้าให้อัตโนมัติเมื่อมีอันใหม่เข้ามา

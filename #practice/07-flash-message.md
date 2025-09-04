## Flash Message

# app/Http/Middleware/HandleInertiaRequests.php

```php
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'toast' => [
                'message' => session('message')
            ]
        ];
    }
```

# app/Http/Controllers/ProductController.php
```php
    return redirect()
            ->route('products.index')
            ->with('message', 'Product has been created successfully.');
    
    //using in Store, Update, Destroy

```

# resources/js/Components/toast/Toast.vue
```vue
<template>
    <transition :name="transitionName">
      <div class="toast" :class="toastClasses" v-show="show">
        <div class="toast-icon flex justify-center items-center">
          <component :is="toastIcon"></component>
        </div>
        <div class="toast-content">
          <div class="toast-title">{{ toastTitle }}</div>
          <div class="toast-message">{{ message }}</div>
        </div>
        <button class="toast-button" @click="$emit('hide')">&times;</button>
      </div>
    </transition>
  </template>
  
  <script>
  import IconError from "./IconError.vue";
  import IconWarning from "./IconWarning.vue";
  import IconSuccess from "./IconSuccess.vue";
  
  export default {
    emits: ["hide"],
    data: () => ({
      timeout: null,
    }),
    watch: {
      show() {
        if (this.timeout) {
          clearTimeout(this.timeout);
        }
  
        this.timeout = setTimeout(() => {
          this.$emit("hide");
        }, 3000);
      },
    },
    props: {
      message: {
        type: String,
        required: true,
      },
      title: {
        type: String,
        default: null,
      },
      show: {
        type: Boolean,
        default: false,
      },
      type: {
        type: String,
        default: "success",
        validator: function (value) {
          return ["success", "warning", "error"].indexOf(value) !== -1;
        },
      },
      position: {
        type: String,
        default: "bottom-right",
      },
    },
    computed: {
      transitionName() {
        const transitions = {
          "top-left": "ltr",
          "bottom-left": "ltr",
          "top-right": "rtl",
          "bottom-right": "rtl",
        };
        return transitions[this.getPosition];
      },
      toastType() {
        return `toast-${this.getType}`;
      },
      toastIcon() {
        return `icon-${this.getType}`;
      },
      getType() {
        return ["success", "warning", "error"].indexOf(this.type) === -1
          ? "success"
          : this.type;
      },
      getPosition() {
        return ["bottom-left", "bottom-right", "top-left", "top-right"].indexOf(
          this.position
        ) === -1
          ? "bottom-right"
          : this.position;
      },
      toastClasses() {
        return [this.toastType, this.getPosition];
      },
      toastTitle() {
        return this.title
          ? this.title
          : this.type.charAt(0).toUpperCase() + this.type.slice(1);
      },
    },
    components: {
      IconError,
      IconWarning,
      IconSuccess,
    },
  };
  </script>
  
  <style scoped>
  .toast {
    width: 400px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    box-shadow: 1px 5px 10px -5px rgba(0, 0, 0, 0.2);
    position: relative;
  }
  
  .toast::before {
    content: "";
    width: 4px;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
  }
  
  .toast-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    padding: 7px;
  }
  
  .toast-success .toast-icon svg {
    fill: #ecfdf5;
  }
  
  .toast-success {
    background: #ecfdf5;
  }
  
  .toast-success::before,
  .toast-success .toast-icon {
    background: #34d399;
  }
  
  .toast-warning .toast-icon svg {
    fill: #fffbeb;
  }
  
  .toast-warning {
    background: #fffbeb;
  }
  
  .toast-warning::before,
  .toast-warning .toast-icon {
    background: #f59e0b;
  }
  
  .toast-error .toast-icon svg {
    fill: #f3f2f2;
  }
  
  .toast-error {
    background: #fef2f2;
  }
  
  .toast-error::before,
  .toast-error .toast-icon {
    background: #ef4444;
  }
  
  .toast-content {
    flex-grow: 1;
    margin: 0 1rem;
  }
  
  .toast-title {
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  
  .toast-message {
    font-size: 14px;
    color: #6b7280;
  }
  
  .toast-button {
    width: 1.5em;
    height: 1.5em;
    border: none;
    padding: 0;
    color: #9ca3af;
    opacity: 0.7;
    background: transparent;
    cursor: pointer;
    font-size: 1.5em;
  }
  
  .toast-button:hover {
    opacity: 1;
  }
  
  .bottom-left {
    position: fixed;
    left: 2rem;
    bottom: 2rem;
  }
  .top-left {
    position: fixed;
    left: 2rem;
    top: 2rem;
  }
  .bottom-right {
    position: fixed;
    right: 2rem;
    bottom: 2rem;
  }
  .top-right {
    position: fixed;
    right: 2rem;
    top: 2rem;
  }
  .rtl-enter-active,
  .rtl-leave-active,
  .ltr-enter-active,
  .ltr-leave-active {
    transition: all 0.5s ease-in-out;
  }
  .rtl-enter-from,
  .rtl-leave-to {
    transform: translateX(100%);
  }
  .ltr-enter-from,
  .ltr-leave-to {
    transform: translateX(-100%);
  }
  .rtl-leave-to,
  .ltr-leave-to {
    opacity: 0;
  }
  </style>
```
resources/js/Components/toast/IconError.vue
```vue
<template>
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="16"
      height="16"
      fill="currentColor"
      class="bi bi-x-lg"
      viewBox="0 0 16 16"
    >
      <path
        d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"
      />
    </svg>
  </template>
```
resources/js/Components/toast/IconSuccess.vue
```vue
<template>
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="16"
      height="16"
      fill="currentColor"
      class="bi bi-check-lg"
      viewBox="0 0 16 16"
    >
      <path
        d="M13.485 1.431a1.473 1.473 0 0 1 2.104 2.062l-7.84 9.801a1.473 1.473 0 0 1-2.12.04L.431 8.138a1.473 1.473 0 0 1 2.084-2.083l4.111 4.112 6.82-8.69a.486.486 0 0 1 .04-.045z"
      />
    </svg>
  </template>
  ```

  resources/js/Components/toast/IconWarning.vue
  ```vue
<template>
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="16"
      height="16"
      fill="currentColor"
      class="bi bi-exclamation-lg"
      viewBox="0 0 16 16"
    >
      <path
        d="M6.002 14a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm.195-12.01a1.81 1.81 0 1 1 3.602 0l-.701 7.015a1.105 1.105 0 0 1-2.2 0l-.7-7.015z"
      />
    </svg>
  </template>
  ```


  resources/js/Layouts/AuthenticatedLayout.vue

  ```vue

<script setup>
import Toast from '@/Components/toast/Toast.vue';
import { Link, usePage, router } from '@inertiajs/vue3';


const showingToast = ref(false);

router.on('finish', () => {
    showingToast.value = !!usePage().props.toast.message
})

</script>

<template>

        <Toast 
            :message="$page.props.toast.message || ''"
            :show="showingToast"
            @hide="showingToast = false"
            type="success"
            position="bottom-right"
        />
</template>
  ```


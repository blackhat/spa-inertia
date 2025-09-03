# Some assets for SPA project with Laravel 11, Vue 3 & Inertia

## Table

```html
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Product name
                </th>
                <th scope="col" class="px-6 py-3">
                    Color
                </th>
                <th scope="col" class="px-6 py-3">
                    Category
                </th>
                <th scope="col" class="px-6 py-3">
                    Price
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-white border-b hover:bg-gray-50">
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    Apple MacBook Pro 16"
                </td>
                <td class="px-6 py-4">
                    Laptop
                </td>
                <td class="px-6 py-4">
                    $2999
                </td>
                <td class="px-6 py-4">
                    2100
                </td>
                <td class="px-6 py-4 space-x-2">
                    <a href="#" class="font-medium text-gray-600 hover:underline">Show</a>
                    <a href="#" class="font-medium text-blue-600 hover:underline">Edit</a>
                    <a href="#" class="font-medium text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            <tr class="bg-white border-b hover:bg-gray-50">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    Microsoft Surface Pro
                </th>
                <td class="px-6 py-4">
                    Laptop PC
                </td>
                <td class="px-6 py-4">
                    $1999
                </td>
                <td class="px-6 py-4">
                    879
                </td>
                <td class="px-6 py-4 space-x-2">
                    <a href="#" class="font-medium text-gray-600 hover:underline">Show</a>
                    <a href="#" class="font-medium text-blue-600 hover:underline">Edit</a>
                    <a href="#" class="font-medium text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
        </tbody>
    </table>
    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between py-2 px-4" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-700 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing <span class="font-semibold text-gray-700">1-10</span> of <span class="font-semibold text-gray-700">1000</span></span>
        <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 hover:text-gray-700">Previous</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 hover:text-gray-700">1</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 hover:text-gray-700">2</a>
            </li>
            <li>
                <a href="#" aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 hover:text-blue-700">3</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 hover:text-gray-700">4</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 hover:text-gray-700">5</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-stone-400 hover:text-stone-400 cursor-not-allowed">Next</a>
            </li>
        </ul>
    </nav>
</div>
```

## Form

```html
<div class="flex items-center justify-center">
    <div class="relative w-full max-w-2xl max-h-full">
        <form class="relative bg-white rounded-lg shadow">
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-6">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Product name</label>
                        <input type="text" name="name" id="name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                        <!-- <label for="name" class="block mb-2 text-sm font-medium text-red-700">Product name</label>
                        <input type="text" name="name" id="name" class="shadow-sm bg-red-50 border border-red-500 text-red-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                        <div class="font-sm text-red-500 mt-2">Product name field is required</div> -->
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 ">Brand</label>
                        <input type="text" name="brand" id="brand" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 ">Category</label>
                        <select name="category_id" id="category_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                            <option value="">Category 1</option>
                            <option value="">Category 2</option>
                            <option value="">Category 3</option>
                        </select>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 ">Price</label>
                        <input type="number" name="price" id="price" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="weight" class="block mb-2 text-sm font-medium text-gray-900 ">Weight</label>
                        <input type="number" name="weight" id="weight" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 ">Description</label>
                        <textarea name="description" id="description" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" rows="3"></textarea>
                    </div>
                    <div class="col-span-6 sm:col-span-6 space-x-2">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save</button>
                        <button type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
```

## Details

```html
<div class="relative bg-white rounded-lg shadow">
  <div class="p-6 space-y-6">
      <div class="grid grid-cols-6 gap-6">
          <div class="col-span-6 sm:col-span-3">
              <label class="block mb-2 text-sm font-medium text-gray-900 ">Product name</label>
              <p class="text-sm text-gray-900 w-full py-2.5">MacBook Pro M3 </p>                                       
          </div>
          <div class="col-span-6 sm:col-span-3">
              <label class="block mb-2 text-sm font-medium text-gray-900 ">Brand</label>
              <p class="text-sm text-gray-900 w-full py-2.5">Apple</p>
          </div>
          <div class="col-span-6 sm:col-span-3">
              <label class="block mb-2 text-sm font-medium text-gray-900 ">Category</label>
              <p class="text-sm text-gray-900 w-full py-2.5">Laptop</p>
          </div>
          <div class="col-span-6 sm:col-span-3">
              <label class="block mb-2 text-sm font-medium text-gray-900 ">Price</label>
              <p class="text-sm text-gray-900 w-full py-2.5">$3500</p>
          </div>
          <div class="col-span-6 sm:col-span-3">
              <label class="block mb-2 text-sm font-medium text-gray-900 ">Weight (kg)</label>
              <p class="text-sm text-gray-900 w-full py-2.5">1.5</p>
          </div>
          <div class="col-span-6 sm:col-span-6">
              <label class="block mb-2 text-sm font-medium text-gray-900 ">Description</label>
              <p class="text-sm text-gray-900 w-full py-2.5">MacBook Pro M3 specification</p>
          </div>
          <div class="col-span-6 sm:col-span-6 space-x-2">
              <a href="#" class="text-white bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Edit</a>
              <a href="#" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</a>
          </div>
      </div>
  </div>
</div>
```

## Buttons

```html
<!-- blue -->
<a href="#" class="px-3 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">New Product</a>

<!-- gray -->
<a href="#" class="font-medium rounded-md text-sm px-5 py-2.5 text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200">Cancel</a>

<!-- red -->
<button type="button" class="px-3 py-2.5 text-sm font-medium text-center text-white bg-red-300 rounded-md cursor-not-allowed" disabled>Delete Selected</button>

<!-- cyan -->
<button type="button" class="px-3 py-2.5 text-sm font-medium text-center text-white bg-cyan-300 rounded-md cursor-not-allowed" disabled>Edit Selected</button>
```

## Search form

```html
<div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pb-6">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
        </div>
        <input type="text" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search for items">
    </div>
</div>
```

## Sortable column styles

```css
th a {
    /* display: block;
    width: 100%; */
    color: inherit;
}
th a:hover {
    text-decoration: none;
}
a.sortable {
    padding-right: 20px;
    position: relative;
}
a.sortable:before,
a.sortable:after {
    border: 4px solid transparent;
    content: "";
    display: block;
    height: 0;
    right: 5px;
    top: 50%;
    position: absolute;
    width: 0;
}
a.sortable:before {
    border-bottom-color: #e4e4e4;
    margin-top: -9px;
}
a.sortable.asc:before {
    border-bottom-color: #848484;
    margin-top: -9px;
}
a.sortable:after {
    border-top-color: #e4e4e4;
    margin-top: 1px;
}
a.sortable.desc:after {
    border-top-color: #848484;
    margin-top: 1px;
}
```

## Form modal

```html
<form class="relative bg-white rounded-lg shadow">
    <!-- Modal header -->
    <div class="flex items-start justify-between p-4 border-b rounded-t">
        <h3 class="text-xl font-semibold text-gray-900">
            Bulk Edit
        </h3>
        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="editUserModal">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close modal</span>
        </button>
    </div>
    <!-- Modal body -->
    <div class="p-6 space-y-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6 sm:col-span-6">
                <label class="block mb-2 text-sm font-medium text-gray-900 ">Products</label>
                <ul>
                    <li class="flex items-center">
                        <svg class="w-3.5 h-3.5 me-2 text-cyan-500 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        At least one lowercase character
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3.5 h-3.5 me-2 text-cyan-500 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        At least one lowercase character
                    </li>
                </ul>
            </div>
            <div class="col-span-6 sm:col-span-6">
                <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 ">Category</label>
                <select name="category_id" id="category_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" required="">
                    <option value="">Category 1</option>
                    <option value="">Category 2</option>
                    <option value="">Category 3</option>
                </select>
            </div>
        </div>
    </div>
    <!-- Modal footer -->
    <div class="text-right p-6 space-x-2 border-t rounded-b">
        <button type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</button>
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update</button>
    </div>
</form>
```

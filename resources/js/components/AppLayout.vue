<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div id="app" class="flex min-h-screen flex-col">
        <div class="flex w-full gap-4 bg-gray-100 py-4 px-10">
            <Link href="/products" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
               Products
            </Link>

            <Link href="/products/create" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                Add New Product
            </Link>
            <Link href="/products/archived" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">
                View Archived
            </Link>
        </div>
        <main class="flex-1 p-4">
            <slot />
        </main>
    </div>
</template>

<style scoped></style>

<template>
  <div id="app" class="min-h-screen flex flex-col">
    <header class="bg-gray-50 p-4">
      <nav class="flex gap-4">
        <!-- Boilerplate: Add navigation links here -->
        <Link href="/">Home</Link>
        <Link href="/products">Products</Link>
      </nav>
    </header>
    <main class="flex-1 p-4">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

</script>

<style scoped>
/* Boilerplate: Add styles for the layout */
</style>

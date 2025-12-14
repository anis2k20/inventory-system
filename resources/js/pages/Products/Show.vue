<template>
  <AppLayout>
    <div class="mx-auto max-w-4xl p-6">
      <div class="mb-6 flex items-center gap-4">
        <Link href="/products" class="flex items-center text-gray-500 hover:text-blue-500">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path
              fill="currentColor"
              d="m9.55 12l7.35 7.35q.375.375.363.875t-.388.875t-.875.375t-.875-.375l-7.7-7.675q-.3-.3-.45-.675t-.15-.75t.15-.75t.45-.675l7.7-7.7q.375-.375.888-.363t.887.388t.375.875t-.375.875z"
            />
          </svg>
          Back to Products
        </Link>
      </div>

      <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <div>
          <div v-if="product.thumbnail_url" class="mb-6">
            <img :src="product.thumbnail_url" alt="Product image" class="h-96 w-full rounded-lg object-cover shadow-lg" />
          </div>
          <div v-else class="mb-6 flex h-96 items-center justify-center rounded-lg bg-gray-100">
            <span class="text-gray-500">No image available</span>
          </div>
        </div>

        <div class="space-y-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ product.name }}</h1>
            <p class="mt-2 text-lg text-gray-600">SKU: {{ product.sku }}</p>
          </div>

          <div class="space-y-4">
            <div>
              <span class="text-2xl font-bold text-green-600">{{ product.price }} Tk</span>
            </div>

            <div>
              <span class="text-sm font-medium text-gray-700">Stock Quantity:</span>
              <span class="ml-2 text-sm text-gray-900">{{ product.stock_quantity }}</span>
            </div>

            <div v-if="product.description">
              <span class="text-sm font-medium text-gray-700">Description:</span>
              <p class="mt-1 text-sm text-gray-900">{{ product.description }}</p>
            </div>
          </div>

          <div class="flex gap-4 pt-6">
            <Link
              :href="`/products/${product.id}/edit`"
              class="inline-flex items-center rounded-md bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600"
            >
              Edit Product
            </Link>
            <Button @click="openDialog" variant="destructive">
              Delete Product
            </Button>
          </div>
        </div>
      </div>

      <Dialog v-model:open="dialogOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Confirm Deletion</DialogTitle>
            <DialogDescription>
              Are you sure you want to delete this product? This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" @click="closeDialog">Cancel</Button>
            <Button variant="destructive" @click="confirmDelete">Delete</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/components/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const dialogOpen = ref(false);

const openDialog = () => {
  dialogOpen.value = true;
};

const closeDialog = () => {
  dialogOpen.value = false;
};

const confirmDelete = () => {
  router.delete(`/products/${props.product.id}`, {
    onSuccess: () => {
      closeDialog();
    },
  });
};
</script>

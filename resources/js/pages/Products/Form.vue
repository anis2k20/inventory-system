<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto p-6">
      <h1 class="text-2xl font-bold mb-6">{{ mode === 'create' ? 'Create Product' : 'Edit Product' }}</h1>

      <form @submit.prevent="onSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <Input v-model="form.name" placeholder="Product name" class="mt-1" />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">SKU</label>
          <Input v-model="form.sku" placeholder="Product SKU" class="mt-1" />
          <p v-if="form.errors.sku" class="mt-1 text-sm text-red-600">{{ form.errors.sku }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Price</label>
          <Input v-model="form.price" type="number" step="0.01" placeholder="0.00" class="mt-1" />
          <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Stock Quantity</label>
          <Input v-model="form.stock_quantity" type="number" placeholder="0" class="mt-1" />
          <p v-if="form.errors.stock_quantity" class="mt-1 text-sm text-red-600">{{ form.errors.stock_quantity }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <Textarea v-model="form.description" placeholder="Product description" class="mt-1" />
          <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Image</label>
          <Input v-model="form.image" type="file" accept="image/*" class="mt-1" @change="form.image = $event.target.files[0]" />
          <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
        </div>

        <div class="mt-6 flex gap-4">
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : (mode === 'create' ? 'Create Product' : 'Update Product') }}
          </Button>
          <Link href="/products" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps({
  product: {
    type: Object as () => any,
    default: null,
  },
  mode: {
    type: String,
    required: true,
  },
});

const form = useForm({
  name: props.product?.name || '',
  sku: props.product?.sku || '',
  price: props.product?.price || '',
  stock_quantity: props.product?.stock_quantity || '',
  description: props.product?.description || '',
  image: null,
});

const onSubmit = () => {
  if (props.mode === 'create') {
    form.post('/products', {
      onSuccess: () => {
        form.reset();
      },
    });
  } else {
    form.put(`/products/${props.product.id}`, {
      onSuccess: () => {
        // Optional: handle success
      },
    });
  }
};
</script>

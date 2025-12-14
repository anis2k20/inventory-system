<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl p-6">
            <h1 class="mb-6 flex items-center gap-2 text-2xl font-bold">

                    <Link href="/products" class="flex items-center text-gray-500 hover:text-blue-500 border p-2 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                fill="currentColor"
                                d="m9.55 12l7.35 7.35q.375.375.363.875t-.388.875t-.875.375t-.875-.375l-7.7-7.675q-.3-.3-.45-.675t-.15-.75t.15-.75t.45-.675l7.7-7.7q.375-.375.888-.363t.887.388t.375.875t-.375.875z"
                            />
                        </svg>
                        </Link
                    >

                {{ mode === 'create' ? 'Create Product' : 'Edit Product' }}
            </h1>

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
                    <Input ref="fileInput" type="file" accept="image/*" class="mt-1" @change="handleFileChange" />
                    <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
                    <div v-if="previewUrl" class="mt-4">
                        <img :src="previewUrl" alt="Product preview" class="max-h-48 max-w-xs rounded-md object-cover shadow-md" />
                        <Button type="button" variant="outline" size="sm" class="mt-2" @click="removeImage"> Remove Image </Button>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : mode === 'create' ? 'Create Product' : 'Update Product' }}
                    </Button>
                    <Link
                        href="/products"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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

const fileInput = ref(null);
const previewUrl = ref(null);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.image = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewUrl.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    previewUrl.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

watch(
    () => props.product,
    (newProduct) => {
        if (newProduct) {
            form.name = newProduct.name;
            form.sku = newProduct.sku;
            form.price = newProduct.price;
            form.stock_quantity = newProduct.stock_quantity;
            form.description = newProduct.description;
            previewUrl.value = newProduct.image_url || null;
        }
    },
    { immediate: true },
);

const onSubmit = () => {
    if (props.mode === 'create') {
        form.post('/products', {
            onSuccess: () => {
                form.reset();
                previewUrl.value = null;
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

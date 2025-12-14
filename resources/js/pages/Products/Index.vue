<template>
    <AppLayout>
        <div class="mx-auto p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold">Products</h1>
                <Link href="/products/create" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    Add New Product
                </Link>
            </div>

            <div class="mb-6">
                <Input v-model="search" @input="debouncedSearch" placeholder="Search products..." class="max-w-sm" />
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-5">
                <Card v-for="product in products.data" :key="product.id" class="transition-shadow hover:shadow-lg"
                    ><CardHeader>
                        <div v-if="product.image_url" class="mb-4 w-full">
                            <img :src="product.image_url" alt="Product image" class="h-32 max-w-full rounded-md object-cover" />
                        </div>
                        <CardTitle>{{ product.name }}</CardTitle>
                        <CardDescription>SKU: {{ product.sku }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="mb-2 text-sm text-gray-600">{{ product.description }}</p>
                        <p class="text-lg font-semibold text-green-600">${{ product.price }}</p>
                        <p class="text-sm">Stock: {{ product.stock_quantity }}</p>
                    </CardContent>
                    <CardFooter class="flex gap-2">
                        <Link
                            :href="`/products/${product.id}/edit`"
                            class="inline-flex items-center rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600"
                        >
                            Edit
                        </Link>
                        <Button @click="deleteProduct(product.id)" variant="destructive" size="sm"> Delete </Button>
                    </CardFooter>
                </Card>
            </div>

            <div class="mt-10 flex justify-center">
                <Pagination v-if="products.last_page > 1" :items-per-page="50">
                    <PaginationContent>
                        <PaginationItem v-if="products.prev_page_url" class="mr-1">
                            <PaginationPrevious @click="goToPage(products.current_page - 1)" />
                        </PaginationItem>

                        <PaginationItem v-for="page in visiblePages" :key="page" class="mr-1">
                            <PaginationLink :is-active="page === products.current_page" @click="goToPage(page)">
                                {{ page }}
                            </PaginationLink>
                        </PaginationItem>

                        <PaginationItem v-if="products.next_page_url">
                            <PaginationNext @click="goToPage(products.current_page + 1)" />
                        </PaginationItem>
                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/components/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');
const currentPage = ref(props.products.current_page);

const debouncedSearch = (() => {
    let timeout: ReturnType<typeof setTimeout>;
    return () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            router.get('/products', { search: search.value, page: 1 }, { preserveState: true });
        }, 300);
    };
})();

const goToPage = (page: number) => {
    currentPage.value = page;
    router.get('/products', { page, search: search.value }, { preserveState: true });
};

const deleteProduct = (id: number) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(`/products/${id}`);
    }
};

watch(
    () => props.filters.search,
    (newSearch) => {
        search.value = newSearch || '';
    },
);

watch(
    () => props.products.current_page,
    (newPage) => {
        currentPage.value = newPage;
    },
);

const visiblePages = computed(() => {
    const pages = [];
    const totalPages = props.products.last_page;

    let startPage = Math.max(1, currentPage.value - 2);
    let endPage = Math.min(totalPages, currentPage.value + 2);

    if (currentPage.value <= 3) {
        endPage = Math.min(5, totalPages);
    } else if (currentPage.value > totalPages - 3) {
        startPage = Math.max(1, totalPages - 4);
    }

    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    return pages;
});
</script>

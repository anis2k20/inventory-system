<template>
    <AppLayout>
        <div class="mx-auto p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold">Products</h1>

                <Input v-model="search" @input="debouncedSearch" placeholder="Search products..." class="max-w-sm" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
                <Card v-for="product in products.data" :key="product.id" class="transition-shadow hover:shadow-lg"
                    ><CardHeader>
                        <div v-if="product.thumbnail_url" class="mb-4 w-full">
                            <img :src="product.thumbnail_url" alt="Product image" class="h-40 w-full rounded-md border object-cover" />
                        </div>
                        <CardTitle>{{ product.name }}</CardTitle>
                        <CardDescription>SKU: {{ product.sku }}</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-1">
                        <p class="text-lg font-semibold text-green-600">{{ product.price }} Tk</p>
                        <p class="text-sm">Stock: {{ product.stock_quantity }}</p>
                    </CardContent>
                    <CardFooter class="flex gap-2">
                        <Link
                            :href="`/products/${product.id}/edit`"
                            class="inline-flex items-center rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600"
                        >
                            Edit
                        </Link>
                        <Button @click="openDialog(product.id)" variant="destructive" size="sm"> Delete </Button>
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
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
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
const dialogOpen = ref(false);
const productIdToDelete = ref<number | null>(null);

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

const openDialog = (id: number) => {
    productIdToDelete.value = id;
    dialogOpen.value = true;
};

const closeDialog = () => {
    dialogOpen.value = false;
    productIdToDelete.value = null;
};

const confirmDelete = () => {
    if (productIdToDelete.value !== null) {
        router.delete(`/products/${productIdToDelete.value}`, {
            onSuccess: () => {
                closeDialog();
            },
        });
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

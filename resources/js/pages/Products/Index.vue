<template>
    <AppLayout>
        <div class="mx-auto p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold">Products</h1>

                <Input v-model="search" @input="debouncedSearch" placeholder="Search products..." class="max-w-sm" />
            </div>

            <div class="mb-4 overflow-hidden rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">Image</TableHead>
                            <TableHead class="w-[200px]">Name</TableHead>
                            <TableHead class="w-[120px]">SKU</TableHead>
                            <TableHead class="w-[100px]">Price</TableHead>
                            <TableHead class="w-[100px]">Stock</TableHead>
                            <TableHead class="w-[200px]">Description</TableHead>
                            <TableHead class="w-[150px] text-center">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="product in products.data" :key="product.id">
                            <TableCell>
                                <div v-if="product.thumbnail_url" class="h-20 w-20">
                                    <img :src="product.thumbnail_url" alt="Product image" class="h-full w-full rounded-md object-cover" />
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="font-medium">{{ product.name }}</div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm text-gray-500">{{ product.sku }}</div>
                            </TableCell>
                            <TableCell>
                                <div class="text-lg font-semibold text-green-600">{{ product.price }} Tk</div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm">Stock: {{ product.stock_quantity }}</div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm text-gray-700">
                                    {{ product.description ? product.description : 'N/A' }}
                                </div>
                            </TableCell>
                            <TableCell class="text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="`/products/${product.id}/edit`"
                                        class="inline-flex items-center rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600"
                                    >
                                        Edit
                                    </Link>
                                    <Button @click="openDialog(product.id)" variant="destructive" size="sm"> Delete </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
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
                        <DialogDescription> Are you sure you want to delete this product? This action cannot be undone. </DialogDescription>
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
import { Input } from '@/components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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

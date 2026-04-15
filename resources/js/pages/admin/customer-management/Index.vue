<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash, Eye } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { create, destroy, edit, index, show } from '@/routes/admin/customer';
import { h } from 'vue';


interface Customer {
    id: number;
    name: string;
    email: string;
    status: string;
    created_at: string;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

const props = withDefaults(defineProps<{
    data: Customer[];
    pagination: Pagination;
    offset: number;
    filters?: Record<string, string> | null;
    search?: string | null;
    sort_by?: string | null;
    sort_order?: 'asc' | 'desc' | null;
}>(), {
    filters: () => ({}),
    search: '',
    sort_by: '',
    sort_order: 'asc',
});

const normalizedFilters = computed<Record<string, string>>(() => props.filters ?? {});
const normalizedSearch = computed<string>(() => props.search ?? '');
const normalizedSortBy = computed<string>(() => props.sort_by ?? '');
const normalizedSortOrder = computed<'asc' | 'desc'>(() => (props.sort_order === 'desc' ? 'desc' : 'asc'));

const updateParams = (params: Record<string, string | number | object>) => {
    router.get(
        index.url(),
        {
            search: normalizedSearch.value,
            filters: normalizedFilters.value,
            sort_by: normalizedSortBy.value,
            sort_order: normalizedSortOrder.value,
            per_page: props.pagination.per_page,
            ...params,
        },
        { preserveState: true, replace: true },
    );
};

const deleteCustomer = (customerId: number) => {
    if (!confirm('Are you sure you want to delete this customer?')) {
        return;
    }

    router.delete(destroy.url(customerId));
};

defineOptions({
    layout: {
        name: 'AdminSidebar',
        breadcrumbs: [
            {
                title: 'Customer Management',
                href: index.url(),
            },
            {
                title: 'Customer List',
                href: index.url(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Customer Management" />

    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Customer Management</h1>
            <Button as-child>
                <Link :href="create.url()">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Customer
                </Link>
            </Button>
        </div>

        <DataTable
            :data="data"
            :pagination="pagination"
            :offset="offset"
            :search-value="normalizedSearch"
            :filter-values="normalizedFilters"
            :sort-by="normalizedSortBy"
            :sort-order="normalizedSortOrder"
            :columns="[
                { key: 'name', label: 'Name', sortable: true },
                { key: 'email', label: 'Email', sortable: true },
                {
                    key: 'status',
                    label: 'Status',
                    sortable: true,
                    render: (item: Customer) =>
                        h(
                            'span',
                            {
                                class: item.status === 'active'
                                    ? 'text-emerald-600 font-semibold'
                                    : 'text-red-600 font-semibold',
                            },
                            item.status.charAt(0).toUpperCase() + item.status.slice(1),
                        ),
                }
            ]"
            :filters="[
                {
                    key: 'status',
                    label: 'Status',
                    placeholder: 'Filter status',
                    options: [
                        { label: 'Active', value: 'active' },
                        { label: 'Inactive', value: 'inactive' },
                    ],
                },
            ]"
            :actions="[
                {
                    label: 'View',
                    icon: Eye,
                    onClick: (item: Customer) => router.visit(show.url(item.id)),
                },
                {
                    label: 'Edit',
                    icon: Pencil,
                    onClick: (item: Customer) => router.visit(edit.url(item.id)),
                },
                {
                    label: 'Delete',
                    icon: Trash,
                    variant: 'destructive',
                    onClick: (item: Customer) => deleteCustomer(item.id),
                },
            ]"
            @search="(value: string) => updateParams({ search: value, page: 1 })"
            @filterChange="(value: object) => updateParams({ filters: value, page: 1 })"
            @sort="(column: string, order: 'asc' | 'desc') => updateParams({ sort_by: column, sort_order: order, page: 1 })"
            @perPageChange="(value: number) => updateParams({ per_page: value, page: 1 })"
            @pageChange="(page: number) => updateParams({ page })"
        />
    </div>
</template>
<script setup>
import { router } from '@inertiajs/vue3';
import DataTable from '@/Components/DataTable.vue';

const props = defineProps({
  customers: Array,
  pagination: Object,
  offset: Number,
  filters: Object,
  search: String,
  sortBy: String,
  sortOrder: String,
});

const handleParamsChange = (newParams) => {
  router.get(route('admin.customers.index'), {
    search: props.search,
    filters: props.filters,
    sort_by: props.sortBy,
    sort_order: props.sortOrder,
    per_page: props.pagination.per_page,
    ...newParams
  }, { preserveState: true, replace: true });
};
</script>

<template>
  <DataTable
    :data="customers"
    :pagination="pagination"
    :search-value="search"
    :columns="[
      { key: 'first_name', label: 'First Name', sortable: true },
      { key: 'email', label: 'Email', sortable: true },
    ]"
    @search="(val) => handleParamsChange({ search: val, page: 1 })"
    @sort="(key, order) => handleParamsChange({ sort_by: key, sort_order: order })"
    @pageChange="(page) => handleParamsChange({ page })"
  />
</template>
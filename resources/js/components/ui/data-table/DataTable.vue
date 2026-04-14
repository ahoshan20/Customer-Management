<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { 
  ChevronUp, 
  ChevronDown, 
  Search, 
  X, 
  Settings, 
  ChevronLeft, 
  ChevronRight 
} from 'lucide-vue-next';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

// Define Types (matching your React DataTableProps)
interface Column<T> {
  key: string;
  label: string;
  sortable?: boolean;
  className?: string;
  render?: (item: T, index: number) => any;
}

interface Filter {
  key: string;
  label: string;
  placeholder?: string;
  options: { label: string; value: string | number }[];
}

interface Action<T> {
  label: string | ((item: T) => string);
  icon?: any | ((item: T) => any);
  onClick: (item: T, index: number) => void;
  show?: (item: T) => boolean;
  variant?: 'default' | 'destructive';
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
  data: any[];
  columns: Column<any>[];
  pagination: Pagination;
  offset?: number;
  showNumbering?: boolean;
  numberingKey?: string;
  filters?: Filter[];
  actions?: Action<any>[];
  searchValue?: string;
  filterValues?: Record<string, any>;
  sortBy?: string;
  sortOrder?: 'asc' | 'desc';
  isLoading?: boolean;
  emptyMessage?: string;
  searchPlaceholder?: string;
}>(), {
  offset: 0,
  showNumbering: true,
  filters: () => [],
  actions: () => [],
  searchValue: '',
  filterValues: () => ({}),
  sortBy: '',
  sortOrder: 'asc',
  isLoading: false,
  emptyMessage: 'No data available',
  searchPlaceholder: 'Search...',
});

const emit = defineEmits([
  'search', 
  'filterChange', 
  'sort', 
  'perPageChange', 
  'pageChange'
]);

// Local State
const localSearch = ref(props.searchValue);
const localFilters = ref({ ...props.filterValues });

// Debounced Search Logic
let timeout: ReturnType<typeof setTimeout>;
watch(localSearch, (newValue) => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    if (newValue !== props.searchValue) {
      emit('search', newValue);
    }
  }, 500);
});

// Helper Methods
const handleFilterChange = (key: string, value: unknown) => {
  localFilters.value[key] = value === null ? '' : String(value);
  emit('filterChange', { ...localFilters.value });
};

const clearFilter = (key: string) => {
  delete localFilters.value[key];
  emit('filterChange', { ...localFilters.value });
};

const handleSort = (columnKey: string) => {
  const column = props.columns.find(col => col.key === columnKey);
  if (!column?.sortable) return;

  const newSortOrder = props.sortBy === columnKey && props.sortOrder === 'asc' ? 'desc' : 'asc';
  emit('sort', columnKey, newSortOrder);
};

const resolveActionIcon = (action: Action<any>, item: any) => {
  if (!action.icon) {
    return null;
  }

  // Vue component icons (e.g. Lucide) are functions too.
  // Treat single-argument functions as item-based icon factories only.
  if (typeof action.icon === 'function' && action.icon.length === 1) {
    return action.icon(item);
  }

  return action.icon;
};

// Pagination Logic
const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let startPage = Math.max(1, props.pagination.current_page - Math.floor(maxVisible / 2));
  const endPage = Math.min(props.pagination.last_page, startPage + maxVisible - 1);

  if (endPage - startPage < maxVisible - 1) {
    startPage = Math.max(1, endPage - maxVisible + 1);
  }
  
  for (let i = startPage; i <= endPage; i++) {
    pages.push(i);
  }
  return { pages, startPage, endPage };
});
</script>

<template>
  <div class="datatable-container">
    <div class="datatable-header">
      <div class="flex flex-wrap items-center gap-2">
        <div class="datatable-search-wrapper">
          <Search class="datatable-search-icon" />
          <Input
            v-model="localSearch"
            type="text"
            :placeholder="searchPlaceholder"
            class="datatable-search-input"
          />
          <button v-if="localSearch" @click="localSearch = ''" class="datatable-search-clear">
            <X class="h-4 w-4" />
          </button>
        </div>

        <div class="datatable-perpage">
          <span class="datatable-perpage-label">Show</span>
          <Select 
            :model-value="String(pagination.per_page)"
            @update:model-value="(val) => emit('perPageChange', Number(val))"
          >
            <SelectTrigger class="datatable-select-small">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="val in [2, 5, 10, 15, 30, 50, 100]" :key="val" :value="String(val)">
                {{ val }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div v-if="filters.length > 0" class="datatable-filters">
          <div v-for="filter in filters" :key="filter.key" class="datatable-filter-item">
            <Select
              :model-value="String(localFilters[filter.key] || '')"
              @update:model-value="(val) => handleFilterChange(filter.key, val)"
            >
              <SelectTrigger class="datatable-select">
                <SelectValue :placeholder="filter.placeholder || filter.label" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="opt in filter.options" :key="opt.value" :value="String(opt.value)">
                  {{ opt.label }}
                </SelectItem>
              </SelectContent>
            </Select>
            <button v-if="localFilters[filter.key]" @click="clearFilter(filter.key)" class="datatable-filter-clear">
              <X class="h-3.5 w-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="datatable-wrapper">
      <table class="datatable">
        <thead class="datatable-thead">
          <tr>
            <th v-if="showNumbering" class="datatable-th datatable-th-number">#</th>
            <th
              v-for="column in columns"
              :key="column.key"
              :class="['datatable-th', column.sortable ? 'datatable-th-sortable' : '', column.className || '']"
              @click="handleSort(column.key)"
            >
              <div class="datatable-th-content">
                <span>{{ column.label }}</span>
                <div v-if="column.sortable" class="datatable-sort-icons">
                  <ChevronUp 
                    :class="['datatable-sort-icon', sortBy === column.key && sortOrder === 'asc' ? 'datatable-sort-icon-active' : '']" 
                  />
                  <ChevronDown 
                    :class="['datatable-sort-icon', sortBy === column.key && sortOrder === 'desc' ? 'datatable-sort-icon-active' : '']" 
                  />
                </div>
              </div>
            </th>
            <th v-if="actions.length > 0" class="datatable-th datatable-th-actions">Actions</th>
          </tr>
        </thead>

        <tbody class="datatable-tbody">
          <tr v-if="isLoading">
            <td :colspan="columns.length + (showNumbering ? 1 : 0) + (actions.length > 0 ? 1 : 0)" class="datatable-cell-center">
              <div class="datatable-loading">
                <div class="datatable-spinner"></div>
                <span>Loading...</span>
              </div>
            </td>
          </tr>

          <tr v-else-if="data.length === 0">
            <td :colspan="columns.length + (showNumbering ? 1 : 0) + (actions.length > 0 ? 1 : 0)" class="datatable-cell-center">
              <div class="datatable-empty">
                <p>{{ emptyMessage }}</p>
              </div>
            </td>
          </tr>

          <tr v-else v-for="(item, index) in data" :key="index" class="datatable-row">
            <td v-if="showNumbering" class="datatable-cell datatable-cell-number">
              {{ numberingKey ? item[numberingKey] : (offset + index + 1) }}
            </td>
            
            <td v-for="column in columns" :key="column.key" :class="['datatable-cell', column.className || '']">
              <component v-if="column.render" :is="column.render(item, index)" />
              <template v-else>{{ item[column.key] }}</template>
            </td>

            <td v-if="actions.length > 0" class="datatable-cell datatable-cell-actions">
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" size="sm" class="datatable-actions-trigger">
                    <Settings class="datatable-actions-icon" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="datatable-actions-menu">
                  <template v-for="(action, aIndex) in actions" :key="aIndex">
                    <DropdownMenuItem 
                      v-if="action.show ? action.show(item) : true"
                      @click="action.onClick(item, index)"
                      :class="['datatable-action-item', action.variant === 'destructive' ? 'datatable-action-item-destructive' : '']"
                    >
                      <component v-if="resolveActionIcon(action, item)" :is="resolveActionIcon(action, item)" class="mr-2 h-4 w-4" />
                      <span>{{ typeof action.label === 'function' ? action.label(item) : action.label }}</span>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator v-if="aIndex < actions.length - 1" />
                  </template>
                </DropdownMenuContent>
              </DropdownMenu>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="datatable-footer">
      <div class="datatable-info">
        Showing <span class="datatable-info-highlight">{{ pagination.from || 0 }}</span> to
        <span class="datatable-info-highlight">{{ pagination.to || 0 }}</span> of
        <span class="datatable-info-highlight">{{ pagination.total }}</span> entries
      </div>
      
      <div class="datatable-pagination">
        <Button
          @click="emit('pageChange', pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          variant="outline" size="sm" class="datatable-pagination-nav"
        >
          <ChevronLeft class="h-4 w-4" />
          <span class="datatable-pagination-nav-text">Previous</span>
        </Button>

        <div class="flex gap-2 items-center">
          <Button v-if="visiblePages.startPage > 1" @click="emit('pageChange', 1)" variant="outline" size="sm">1</Button>
          <span v-if="visiblePages.startPage > 2">...</span>
          
          <Button 
            v-for="p in visiblePages.pages" :key="p"
            @click="emit('pageChange', p)"
            :variant="p === pagination.current_page ? 'default' : 'outline'"
            size="sm"
          >
            {{ p }}
          </Button>

          <span v-if="visiblePages.endPage < pagination.last_page - 1">...</span>
          <Button v-if="visiblePages.endPage < pagination.last_page" @click="emit('pageChange', pagination.last_page)" variant="outline" size="sm">
            {{ pagination.last_page }}
          </Button>
        </div>

        <Button
          @click="emit('pageChange', pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          variant="outline" size="sm" class="datatable-pagination-nav"
        >
          <span class="datatable-pagination-nav-text">Next</span>
          <ChevronRight class="h-4 w-4" />
        </Button>
      </div>
    </div>
  </div>
</template>
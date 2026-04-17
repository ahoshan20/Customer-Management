<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Mail, MapPin, Pencil, ShieldCheck } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { edit, index } from '@/routes/admin/customer';
import type { Address } from '@/types';

type Customer = {
    id: number;
    name: string;
    email: string;
    status: string;
    created_at: string | null;
    email_verified_at: string | null;
};

defineProps<{
    customer: Customer;
    customerAddress: Address | null;
}>();

defineOptions({
    layout: {
        name: 'AdminSidebar',
        breadcrumbs: [
            { title: 'Customer Management', href: index.url() },
            { title: 'Customer Details', href: '#' },
        ],
    },
});

const getStatusVariant = (status: string) => {
    switch (status.toLowerCase()) {
        case 'active':
            return 'default';
        case 'inactive':
            return 'secondary';
        case 'suspended':
            return 'destructive';
        default:
            return 'outline';
    }
};

const formatDate = (value: string | null) => {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleDateString();
};
</script>

<template>
    <Head title="Customer Details" />

    <div class="space-y-6 p-6 max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <Button as-child variant="ghost" size="icon" class="rounded-full">
                    <Link :href="index.url()">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ customer.name }}</h1>
                    <p class="text-muted-foreground">ID: #CUST-{{ customer.id.toString().padStart(5, '0') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Badge :variant="getStatusVariant(customer.status)" class="px-3 py-1 text-sm">
                    {{ customer.status }}
                </Badge>
                <Button as-child variant="outline">
                    <Link :href="edit.url(customer.id)">
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit Customer
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <User class="h-5 w-5 text-primary" />
                            Personal Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <Mail class="h-4 w-4" /> Email Address
                            </p>
                            <p class="font-medium">{{ customer.email }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <Calendar class="h-4 w-4" /> Date Joined
                            </p>
                            <p class="font-medium">{{ formatDate(customer.created_at) }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-muted-foreground flex items-center gap-2">
                                <ShieldCheck class="h-4 w-4" /> Verification
                            </p>
                            <p class="font-medium">{{ customer.email_verified_at ? 'Verified' : 'Unverified' }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MapPin class="h-5 w-5 text-primary" />
                            Primary Address
                        </CardTitle>
                    </CardHeader>
                    <CardContent v-if="customerAddress">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Address Label</p>
                                <p class="capitalize">{{ customerAddress.label || 'Home' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-muted-foreground">Street Address</p>
                                <p>{{ customerAddress.line_1 }}</p>
                                <p v-if="customerAddress.line_2" class="text-muted-foreground italic">{{ customerAddress.line_2 }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">City & State</p>
                                <p>{{ customerAddress.city }}, {{ customerAddress.state || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Postal & Country</p>
                                <p>{{ customerAddress.postal_code || 'N/A' }} - {{ customerAddress.country }}</p>
                            </div>
                        </div>
                    </CardContent>
                    <CardContent v-else class="flex flex-col items-center justify-center py-10 text-muted-foreground">
                        <MapPin class="h-10 w-10 mb-2 opacity-20" />
                        <p>No address record found.</p>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-6">
                <Card class="bg-muted/50">
                    <CardHeader>
                        <CardTitle class="text-lg">Account Summary</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground">Total Orders</span>
                            <span class="font-bold">24</span> </div>
                        <Separator />
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground">Spent to date</span>
                            <span class="font-bold text-primary">$1,240.00</span>
                        </div>
                        <Separator />
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground">Last Activity</span>
                            <span class="text-xs">2 hours ago</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Danger Zone</CardTitle>
                        <CardDescription>Actions are permanent.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <Button variant="outline" class="w-full justify-start text-orange-500 hover:text-orange-600">
                            Suspend Account
                        </Button>
                        <Button variant="destructive" class="w-full justify-start">
                            Delete Data
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
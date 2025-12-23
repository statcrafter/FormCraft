<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    form: {
        id: number;
        title: string;
        description: string | null;
        definition: any;
    };
}>();

const breadcrumbs = [
    { title: 'Mes Formulaires', href: '/forms' },
    { title: props.form.title, href: `/forms/${props.form.id}` },
];
</script>

<template>
    <Head :title="form.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-8">
                <Link href="/forms">
                    <Button variant="ghost" size="icon">
                        <ChevronLeft class="w-4 h-4" />
                    </Button>
                </Link>
                <h1 class="text-3xl font-bold tracking-tight">{{ form.title }}</h1>
            </div>

            <div class="bg-card border rounded-lg p-8 flex flex-col items-center justify-center min-h-[400px]">
                <p class="text-muted-foreground mb-4">L'éditeur de formulaire sera bientôt disponible ici.</p>
                <p class="text-sm text-muted-foreground italic">(Phase 3 du projet)</p>
            </div>
        </div>
    </AppLayout>
</template>

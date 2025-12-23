<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Plus, FileText } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref } from 'vue';

interface FormItem {
    id: number;
    title: string;
    description: string | null;
    form_id: string;
    updated_at: string;
}

defineProps<{
    forms: FormItem[];
}>();

const breadcrumbs = [
    { title: 'Mes Formulaires', href: '/forms' },
];

const showCreateModal = ref(false);

const form = useForm({
    title: '',
    description: '',
});

const submit = () => {
    form.post('/forms', {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Mes Formulaires" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Mes Formulaires</h1>
                    <p class="text-muted-foreground">Gérez vos XLSForms et lancez vos collectes.</p>
                </div>

                <Dialog v-model:open="showCreateModal">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="w-4 h-4 mr-2" />
                            Nouveau Formulaire
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Créer un nouveau formulaire</DialogTitle>
                            <DialogDescription>
                                Donnez un titre à votre formulaire pour commencer.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="submit" class="space-y-4 py-4">
                            <div class="space-y-2">
                                <Label for="title">Titre</Label>
                                <Input id="title" v-model="form.title" placeholder="Ex: Enquête de satisfaction" required />
                            </div>
                            <div class="space-y-2">
                                <Label for="description">Description (optionnelle)</Label>
                                <Input id="description" v-model="form.description" placeholder="Bref résumé du projet..." />
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" @click="showCreateModal = false">Annuler</Button>
                                <Button type="submit" :disabled="form.processing">Créer</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <div v-if="forms.length === 0" class="flex flex-col items-center justify-center min-h-[400px] border-2 border-dashed rounded-lg">
                <FileText class="w-12 h-12 text-muted-foreground mb-4" />
                <h3 class="text-xl font-semibold">Aucun formulaire pour le moment</h3>
                <p class="text-muted-foreground mb-6">Commencez par créer votre premier XLSForm.</p>
                <Button @click="showCreateModal = true">Créer mon premier formulaire</Button>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="item in forms" :key="item.id" class="hover:shadow-md transition-shadow">
                    <CardHeader>
                        <CardTitle>{{ item.title }}</CardTitle>
                        <CardDescription class="line-clamp-2 h-10">
                            {{ item.description || 'Aucune description' }}
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="flex justify-between items-center text-sm text-muted-foreground">
                        <span>Mis à jour le {{ new Date(item.updated_at).toLocaleDateString() }}</span>
                        <div class="flex gap-2">
                            <Link :href="`/forms/${item.id}`">
                                <Button variant="outline" size="sm">Éditer</Button>
                            </Link>
                        </div>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeft, Save } from 'lucide-vue-next';
import { onMounted } from 'vue';
import { useFormEditorStore } from '@/stores/formEditor';
import Toolbox from '@/components/editor/Toolbox.vue';
import Canvas from '@/components/editor/Canvas.vue';
import PropertiesEditor from '@/components/editor/PropertiesEditor.vue';

const props = defineProps<{
    form: any;
}>();

const store = useFormEditorStore();

onMounted(() => {
    store.formTitle = props.form.title;
    store.formId = props.form.form_id;
    store.setQuestions(props.form.definition || []);
});

const inertiaForm = useForm({
    title: '',
    definition: [] as any[],
});

const saveForm = () => {
    inertiaForm.title = store.formTitle;
    inertiaForm.definition = store.questions;
    inertiaForm.put(`/forms/${props.form.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Éditeur - ${store.formTitle}`" />

    <div class="flex flex-col h-screen overflow-hidden bg-background text-foreground">
        <!-- Header -->
        <header class="h-14 border-b flex items-center justify-between px-4 bg-card shrink-0">
            <div class="flex items-center gap-4">
                <Link href="/forms">
                    <Button variant="ghost" size="sm">
                        <ChevronLeft class="w-4 h-4 mr-2" />
                        Retour
                    </Button>
                </Link>
                <div class="h-4 w-px bg-border"></div>
                <h1 class="font-medium truncate max-w-[300px]">{{ store.formTitle }}</h1>
            </div>

            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" @click="saveForm" :disabled="inertiaForm.processing">
                    <Save class="w-4 h-4 mr-2" />
                    {{ inertiaForm.processing ? 'Enregistrement...' : 'Sauvegarder' }}
                </Button>
                <Button size="sm">
                    Publier
                </Button>
            </div>
        </header>

        <!-- Main Editor -->
        <div class="flex-1 flex overflow-hidden">
            <Toolbox />
            <Canvas />
            <PropertiesEditor />
        </div>
    </div>
</template>

<style>
/* Masquer la sidebar du layout global si nécessaire, 
mais ici on ne l'utilise pas donc c'est ok. */
body {
    overflow: hidden;
}
</style>
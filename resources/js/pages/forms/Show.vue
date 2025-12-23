<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeft, Save, Download, Eye } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { useFormEditorStore } from '@/stores/formEditor';
import Toolbox from '@/components/editor/Toolbox.vue';
import Canvas from '@/components/editor/Canvas.vue';
import PropertiesEditor from '@/components/editor/PropertiesEditor.vue';
import PreviewModal from '@/components/editor/PreviewModal.vue';

const props = defineProps<{
    form: any;
}>();

const store = useFormEditorStore();
const showPreview = ref(false);

onMounted(() => {
    store.formTitle = props.form.title;
    store.formId = props.form.form_id;
    store.formVersion = props.form.version || '1';
    if (props.form.settings) {
        store.globalSettings = { ...store.globalSettings, ...props.form.settings };
    }
    store.setQuestions(props.form.definition || []);
    store.setAssets(props.form.assets || []);
});

const inertiaForm = useForm({
    title: '',
    form_id: '',
    version: '',
    definition: [] as any[],
    settings: {} as any,
});

const saveForm = () => {
    inertiaForm.title = store.formTitle;
    inertiaForm.form_id = store.formId;
    inertiaForm.version = store.formVersion;
    inertiaForm.definition = store.questions;
    inertiaForm.settings = store.globalSettings;
    
    inertiaForm.put(`/forms/${props.form.id}`, {
        preserveScroll: true,
    });
};

const downloadXls = () => {
    window.location.href = `/forms/${props.form.id}/download`;
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
                <Button variant="outline" size="sm" @click="showPreview = true">
                    <Eye class="w-4 h-4 mr-2" />
                    Prévisualiser
                </Button>
                <div class="h-4 w-px bg-border mx-1"></div>
                <Button variant="outline" size="sm" @click="downloadXls">
                    <Download class="w-4 h-4 mr-2" />
                    Exporter XLSForm
                </Button>
                <div class="h-4 w-px bg-border mx-1"></div>
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
            <PropertiesEditor :form="form" />
        </div>

        <!-- Modals -->
        <PreviewModal v-model:open="showPreview" />
    </div>
</template>

<style>
body {
    overflow: hidden;
}
</style>
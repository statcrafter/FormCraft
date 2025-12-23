<script setup lang="ts">
import { useFormEditorStore } from '@/stores/formEditor';
import { Button } from '@/components/ui/button';
import { Plus, Trash2, Image as ImageIcon, Music, Video } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    modelValue?: string;
    type: 'image' | 'audio' | 'video';
    formId: number;
}>();

const emit = defineEmits(['update:modelValue']);
const store = useFormEditorStore();
const fileInput = ref<HTMLInputElement | null>(null);

const uploadForm = useForm({
    file: null as File | null,
    type: props.type,
});

const onFileSelected = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        uploadForm.file = target.files[0];
        uploadForm.post(`/forms/${props.formId}/assets`, {
            preserveScroll: true,
            onSuccess: () => {
                const filename = target.files![0].name;
                emit('update:modelValue', filename);
                uploadForm.reset();
            }
        });
    }
};
</script>

<template>
    <div class="space-y-2">
        <!-- Fichier sélectionné -->
        <div v-if="modelValue" class="flex items-center justify-between p-2 border rounded bg-muted/20 border-primary/20">
            <div class="flex items-center gap-2 overflow-hidden">
                <ImageIcon v-if="type === 'image'" class="w-4 h-4 shrink-0 text-primary" />
                <Music v-if="type === 'audio'" class="w-4 h-4 shrink-0 text-primary" />
                <Video v-if="type === 'video'" class="w-4 h-4 shrink-0 text-primary" />
                <span class="text-[10px] truncate font-mono text-muted-foreground">{{ modelValue }}</span>
            </div>
            <Button variant="ghost" size="icon" class="h-6 w-6 text-muted-foreground hover:text-destructive" @click="emit('update:modelValue', '')">
                <Trash2 class="w-3 h-3" />
            </Button>
        </div>

        <div v-else class="space-y-2">
            <!-- Liste des fichiers déjà présents -->
            <div v-if="store.assets.filter(a => a.type === props.type).length > 0" class="space-y-1">
                <select 
                    class="w-full h-8 text-[10px] rounded-md border border-input bg-background px-2 focus:ring-1 focus:ring-primary outline-none"
                    @change="e => emit('update:modelValue', (e.target as HTMLSelectElement).value)"
                >
                    <option value="">Utiliser un fichier existant...</option>
                    <option v-for="asset in store.assets.filter(a => a.type === props.type)" :key="asset.id" :value="asset.filename">
                        {{ asset.filename }}
                    </option>
                </select>
            </div>

            <Button 
                variant="outline" 
                size="sm" 
                class="w-full text-[10px] h-7 border-dashed border-muted-foreground/30 hover:bg-primary/5 hover:text-primary hover:border-primary/30" 
                @click="fileInput?.click()"
                :disabled="uploadForm.processing"
            >
                <Plus class="w-3 h-3 mr-1" />
                {{ uploadForm.processing ? 'Chargement...' : `Uploader ${type}` }}
            </Button>
            <input type="file" ref="fileInput" class="hidden" @change="onFileSelected" />
        </div>
    </div>
</template>

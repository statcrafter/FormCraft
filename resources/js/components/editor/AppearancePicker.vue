<script setup lang="ts">
import { computed } from 'vue';
import { LayoutList, ListVideo, AlignJustify, Grid3X3, Columns } from 'lucide-vue-next';

const props = defineProps<{
    modelValue?: string;
    type: string;
}>();

const emit = defineEmits(['update:modelValue']);

// Définition des styles visuels par type de question
const options = computed(() => {
    switch (props.type) {
        case 'select_one':
        case 'select_multiple':
            return [
                { label: 'Liste verticale', value: '', icon: LayoutList },
                { label: 'Menu déroulant', value: 'minimal', icon: AlignJustify },
                { label: 'Boutons compacts', value: 'quickcompact', icon: Grid3X3 },
                { label: 'Colonnes', value: 'columns', icon: Columns },
            ];
        case 'image':
            return [
                { label: 'Capture photo', value: '', icon: LayoutList },
                { label: 'Signature', value: 'signature', icon: AlignJustify },
                { label: 'Dessin / Annotation', value: 'draw', icon: Grid3X3 },
            ];
        case 'date':
            return [
                { label: 'Calendrier complet', value: '', icon: LayoutList },
                { label: 'Mois et Année', value: 'month-year', icon: AlignJustify },
                { label: 'Année uniquement', value: 'year', icon: Grid3X3 },
            ];
        default:
            return [];
    }
});

const select = (val: string) => {
    emit('update:modelValue', val);
};
</script>

<template>
    <div v-if="options.length > 0" class="grid grid-cols-2 gap-2">
        <button
            v-for="opt in options"
            :key="opt.value"
            @click="select(opt.value)"
            :class="[
                'flex flex-col items-center justify-center p-2 rounded-md border text-[10px] gap-1 transition-all',
                modelValue === opt.value 
                    ? 'border-primary bg-primary/10 text-primary ring-1 ring-primary' 
                    : 'border-border bg-card hover:border-primary/50'
            ]"
        >
            <component :is="opt.icon" class="w-4 h-4" />
            <span class="font-medium text-center leading-tight">{{ opt.label }}</span>
        </button>
    </div>
    <div v-else class="text-[10px] text-muted-foreground italic">
        Aucun style particulier pour ce type.
    </div>
</template>

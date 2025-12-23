<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { 
    Type, Hash, Binary, List, MapPin, 
    Image as ImageIcon, Mic, Video, File, 
    Calendar, Clock, Calculator, HelpCircle 
} from 'lucide-vue-next';
import draggable from 'vuedraggable';

const categories = [
    {
        name: 'Saisie de base',
        items: [
            { type: 'text', label: 'Texte', icon: Type },
            { type: 'integer', label: 'Entier', icon: Hash },
            { type: 'decimal', label: 'Décimal', icon: Binary },
        ]
    },
    {
        name: 'Sélections',
        items: [
            { type: 'select_one', label: 'Choix unique', icon: List },
            { type: 'select_multiple', label: 'Choix multiples', icon: List },
        ]
    },
    {
        name: 'Multimédia',
        items: [
            { type: 'image', label: 'Image', icon: ImageIcon },
            { type: 'audio', label: 'Audio', icon: Mic },
            { type: 'video', label: 'Vidéo', icon: Video },
        ]
    },
    {
        name: 'Avancé',
        items: [
            { type: 'geopoint', label: 'Localisation', icon: MapPin },
            { type: 'calculate', label: 'Calcul', icon: Calculator },
            { type: 'note', label: 'Note', icon: HelpCircle },
        ]
    }
];

// Fonction pour cloner l'élément lors du drag
const cloneQuestion = (item: any) => {
    return {
        ...item,
        id: Math.random().toString(36).substring(7)
    };
};
</script>

<template>
    <div class="w-64 border-r bg-card flex flex-col h-full overflow-y-auto">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-sm uppercase tracking-wider text-muted-foreground">Composants</h2>
        </div>
        
        <div class="flex-1 p-2 space-y-6">
            <div v-for="category in categories" :key="category.name">
                <h3 class="px-2 mb-2 text-xs font-medium text-muted-foreground">{{ category.name }}</h3>
                <draggable
                    :list="category.items"
                    :group="{ name: 'questions', pull: 'clone', put: false }"
                    :clone="cloneQuestion"
                    item-key="type"
                    class="grid grid-cols-1 gap-1"
                >
                    <template #item="{ element }">
                        <div class="flex items-center gap-3 p-2 text-sm rounded-md cursor-grab hover:bg-accent border border-transparent hover:border-border transition-colors group">
                            <div class="p-1.5 rounded bg-muted group-hover:bg-background">
                                <component :is="element.icon" class="w-4 h-4 text-primary" />
                            </div>
                            <span>{{ element.label }}</span>
                        </div>
                    </template>
                </draggable>
            </div>
        </div>
    </div>
</template>

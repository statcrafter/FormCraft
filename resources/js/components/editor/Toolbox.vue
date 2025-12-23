<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { 
    Type, Hash, Binary, List, MapPin, 
    Image as ImageIcon, Mic, Video, File, 
    Calendar, Clock, Calculator, HelpCircle,
    FolderInput, Repeat
} from 'lucide-vue-next';
import draggable from 'vuedraggable';

const categories = [
    {
        name: 'Structure',
        items: [
            { type: 'begin_group', label: 'Groupe', icon: FolderInput },
            { type: 'begin_repeat', label: 'Répétition', icon: Repeat },
        ]
    },
    {
        name: 'Saisie de base',
        items: [
            { type: 'text', label: 'Texte', icon: Type },
            { type: 'integer', label: 'Entier', icon: Hash },
            { type: 'decimal', label: 'Décimal', icon: Binary },
            { type: 'note', label: 'Note', icon: HelpCircle },
        ]
    },
    {
        name: 'Sélections',
        items: [
            { type: 'select_one', label: 'Choix unique', icon: List },
            { type: 'select_multiple', label: 'Choix multiples', icon: List },
            { type: 'rank', label: 'Classement (Rank)', icon: List },
        ]
    },
    {
        name: 'Dates & Heures',
        items: [
            { type: 'date', label: 'Date', icon: Calendar },
            { type: 'time', label: 'Heure', icon: Clock },
            { type: 'datetime', label: 'Date & Heure', icon: Calendar },
        ]
    },
    {
        name: 'Multimédia',
        items: [
            { type: 'image', label: 'Image', icon: ImageIcon },
            { type: 'audio', label: 'Audio', icon: Mic },
            { type: 'video', label: 'Vidéo', icon: Video },
            { type: 'file', label: 'Fichier', icon: File },
        ]
    },
    {
        name: 'Géographie',
        items: [
            { type: 'geopoint', label: 'Point GPS', icon: MapPin },
            { type: 'geotrace', label: 'Tracé (Ligne)', icon: MapPin },
            { type: 'geoshape', label: 'Zone (Polygone)', icon: MapPin },
        ]
    },
    {
        name: 'Avancé',
        items: [
            { type: 'calculate', label: 'Calcul', icon: Calculator },
            { type: 'barcode', label: 'Code-barres / QR', icon: ImageIcon },
            { type: 'acknowledge', label: 'Confirmation', icon: HelpCircle },
        ]
    }
];

// Fonction pour cloner l'élément lors du drag
const cloneQuestion = (item: any) => {
    const uid = Math.random().toString(36).substring(7);
    const newItem = {
        id: uid,
        type: item.type,
        label: `Nouvelle question (${item.label})`,
        name: `${item.type}_${uid}`,
        required: false,
        properties: {},
    } as any;

    if (['select_one', 'select_multiple', 'rank'].includes(item.type)) {
        newItem.choices = [
            { uuid: Math.random().toString(36).substring(7), name: 'option_1', label: 'Option 1' },
            { uuid: Math.random().toString(36).substring(7), name: 'option_2', label: 'Option 2' },
        ];
    }

    if (['begin_group', 'begin_repeat'].includes(item.type)) {
        newItem.children = [];
    }

    return newItem;
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

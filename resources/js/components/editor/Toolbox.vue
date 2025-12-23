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
        name: 'Organisation',
        items: [
            { type: 'begin_group', label: 'Groupe de questions', icon: FolderInput },
            { type: 'begin_repeat', label: 'Liste répétitive', icon: Repeat },
        ]
    },
    {
        name: 'Questions simples',
        items: [
            { type: 'text', label: 'Texte court', icon: Type },
            { type: 'integer', label: 'Nombre entier', icon: Hash },
            { type: 'decimal', label: 'Nombre décimal', icon: Binary },
            { type: 'note', label: 'Message / Note', icon: HelpCircle },
        ]
    },
    {
        name: 'Choix et listes',
        items: [
            { type: 'select_one', label: 'Choix unique', icon: List },
            { type: 'select_multiple', label: 'Choix multiples', icon: List },
            { type: 'rank', label: 'Classement', icon: List },
        ]
    },
    {
        name: 'Dates et heures',
        items: [
            { type: 'date', label: 'Date', icon: Calendar },
            { type: 'time', label: 'Heure', icon: Clock },
            { type: 'datetime', label: 'Date et Heure', icon: Calendar },
        ]
    },
    {
        name: 'Photos et Médias',
        items: [
            { type: 'image', label: 'Prendre une photo', icon: ImageIcon },
            { type: 'audio', label: 'Enregistrement audio', icon: Mic },
            { type: 'video', label: 'Enregistrement vidéo', icon: Video },
            { type: 'file', label: 'Fichier / Document', icon: File },
        ]
    },
    {
        name: 'Localisation GPS',
        items: [
            { type: 'geopoint', label: 'Point GPS', icon: MapPin },
            { type: 'geotrace', label: 'Tracé GPS', icon: MapPin },
            { type: 'geoshape', label: 'Surface GPS', icon: MapPin },
        ]
    },
    {
        name: 'Outils spéciaux',
        items: [
            { type: 'calculate', label: 'Calcul invisible', icon: Calculator },
            { type: 'barcode', label: 'Scan Code-barres', icon: ImageIcon },
            { type: 'acknowledge', label: 'Confirmation (OK)', icon: HelpCircle },
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

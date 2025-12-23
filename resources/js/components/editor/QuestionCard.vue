<script setup lang="ts">
import { useFormEditorStore, type Question } from '@/stores/formEditor';
import { Button } from '@/components/ui/button';
import { 
    Trash2, GripVertical, Settings2, 
    Type, Hash, Binary, List, MapPin, 
    Image as ImageIcon, Mic, Video, File, 
    Calendar, Clock, Calculator, HelpCircle 
} from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const props = defineProps<{
    question: Question;
}>();

const store = useFormEditorStore();

const isSelected = computed(() => store.selectedQuestionId === props.question.id);

import { computed } from 'vue';

const typeIcons: Record<string, any> = {
    text: Type,
    integer: Hash,
    decimal: Binary,
    select_one: List,
    select_multiple: List,
    rank: List,
    image: ImageIcon,
    audio: Mic,
    video: Video,
    file: File,
    date: Calendar,
    time: Clock,
    datetime: Calendar,
    geopoint: MapPin,
    geotrace: MapPin,
    geoshape: MapPin,
    calculate: Calculator,
    note: HelpCircle,
    barcode: ImageIcon,
    acknowledge: HelpCircle,
};

const currentIcon = computed(() => typeIcons[props.question.type] || HelpCircle);
</script>

<template>
    <div 
        @click="store.selectQuestion(question.id)"
        :class="cn(
            'group relative flex items-start gap-4 p-4 border rounded-lg bg-card hover:border-primary/50 transition-all cursor-pointer',
            isSelected ? 'border-primary ring-1 ring-primary' : 'border-border'
        )"
    >
        <div class="mt-1 text-muted-foreground cursor-grab active:cursor-grabbing">
            <GripVertical class="w-4 h-4" />
        </div>

        <div class="flex-1 space-y-1 overflow-hidden">
            <div class="flex items-center gap-2">
                <div class="p-1 rounded bg-muted">
                    <component :is="currentIcon" class="w-3 h-3 text-primary" />
                </div>
                <span class="text-[10px] font-mono text-muted-foreground bg-muted px-1 rounded uppercase">{{ question.type }}</span>
                <span class="text-[10px] font-mono text-muted-foreground truncate">{{ question.name }}</span>
            </div>
            <p class="font-medium">{{ question.label }}</p>
            <p v-if="question.hint" class="text-xs text-muted-foreground">{{ question.hint }}</p>
            
            <div v-if="question.choices && question.choices.length > 0" class="mt-2 space-y-1">
                <div v-for="choice in question.choices.slice(0, 3)" :key="choice.name" class="flex items-center gap-2 text-xs text-muted-foreground">
                    <div class="w-3 h-3 border rounded flex items-center justify-center shrink-0" :class="question.type === 'select_one' ? 'rounded-full' : 'rounded-sm'">
                    </div>
                    <span class="truncate">{{ choice.label }}</span>
                </div>
                <div v-if="question.choices.length > 3" class="text-[10px] text-muted-foreground pl-5">
                    + {{ question.choices.length - 3 }} autres...
                </div>
            </div>

            <div class="flex gap-2 mt-2">
                <span v-if="question.required" class="text-[10px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Requis</span>
            </div>
        </div>

        <div class="flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <Button 
                variant="ghost" 
                size="icon" 
                class="h-8 w-8 text-destructive hover:text-destructive hover:bg-destructive/10"
                @click.stop="store.removeQuestion(question.id)"
            >
                <Trash2 class="w-4 h-4" />
            </Button>
            <Button 
                variant="ghost" 
                size="icon" 
                class="h-8 w-8"
            >
                <Settings2 class="w-4 h-4" />
            </Button>
        </div>
    </div>
</template>

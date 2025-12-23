<script setup lang="ts">
import { useFormEditorStore, type Question } from '@/stores/formEditor';
import { Button } from '@/components/ui/button';
import { Trash2, GripVertical, Settings2 } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const props = defineProps<{
    question: Question;
}>();

const store = useFormEditorStore();

const isSelected = computed(() => store.selectedQuestionId === props.question.id);

import { computed } from 'vue';
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

        <div class="flex-1 space-y-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-mono text-muted-foreground bg-muted px-1 rounded">{{ question.type }}</span>
                <span class="text-xs font-mono text-muted-foreground">{{ question.name }}</span>
            </div>
            <p class="font-medium">{{ question.label }}</p>
            <p v-if="question.hint" class="text-xs text-muted-foreground">{{ question.hint }}</p>
            
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

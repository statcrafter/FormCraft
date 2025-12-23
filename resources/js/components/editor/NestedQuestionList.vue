<script setup lang="ts">
import { type Question } from '@/stores/formEditor';
import draggable from 'vuedraggable';
import QuestionCard from './QuestionCard.vue';

defineProps<{
    questions: Question[];
}>();

defineOptions({
    name: 'NestedQuestionList'
});
</script>

<template>
    <draggable
        :list="questions"
        group="questions"
        item-key="id"
        class="space-y-3 min-h-[20px]"
        ghost-class="opacity-40"
        handle=".cursor-grab"
        :animation="200"
    >
        <template #item="{ element }">
            <div class="relative">
                <QuestionCard :question="element" />
                
                <!-- Zone enfant pour les groupes -->
                <div v-if="element.children" class="ml-8 mt-2 pl-4 border-l-2 border-primary/20">
                    <div class="bg-muted/30 rounded-lg p-2 min-h-[60px]">
                        <div v-if="element.children.length === 0" class="h-full flex items-center justify-center border border-dashed rounded border-muted-foreground/30 p-4">
                            <span class="text-xs text-muted-foreground">Groupe vide (glissez des éléments ici)</span>
                        </div>
                        <NestedQuestionList :questions="element.children" />
                    </div>
                </div>
            </div>
        </template>
    </draggable>
</template>

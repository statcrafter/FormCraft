<script setup lang="ts">
import { useFormEditorStore } from '@/stores/formEditor';
import draggable from 'vuedraggable';
import QuestionCard from './QuestionCard.vue';

const store = useFormEditorStore();
</script>

<template>
    <div class="flex-1 bg-muted/30 p-8 overflow-y-auto">
        <div class="max-w-3xl mx-auto min-h-full bg-background shadow-sm border rounded-lg p-8">
            <div class="mb-8 border-b pb-4">
                <h1 class="text-2xl font-bold bg-transparent border-none focus:outline-none focus:ring-1 focus:ring-primary rounded px-1" contenteditable @blur="e => store.formTitle = (e.target as HTMLElement).innerText">
                    {{ store.formTitle || 'Titre du formulaire' }}
                </h1>
                <p class="text-sm text-muted-foreground mt-1">
                    ID: <span class="font-mono">{{ store.formId }}</span>
                </p>
            </div>

            <draggable
                v-model="store.questions"
                group="questions"
                item-key="id"
                class="space-y-4 min-h-[400px] pb-20"
                ghost-class="opacity-40"
                drag-class="rotate-1"
                handle=".cursor-grab"
            >
                <template #item="{ element }">
                    <QuestionCard :question="element" />
                </template>
                
                <template #footer>
                    <div v-if="store.questions.length === 0" class="flex flex-col items-center justify-center py-32 border-2 border-dashed rounded-xl text-muted-foreground bg-muted/20">
                        <p class="text-lg font-medium">Votre formulaire est vide</p>
                        <p class="text-sm">Glissez un composant depuis la barre latérale pour commencer.</p>
                    </div>
                </template>
            </draggable>
        </div>
    </div>
</template>

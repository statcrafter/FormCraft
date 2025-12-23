<script setup lang="ts">
import { useFormEditorStore } from '@/stores/formEditor';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Separator } from '@/components/ui/separator';
import { computed } from 'vue';

const store = useFormEditorStore();

const question = computed(() => store.selectedQuestion);

const update = (key: string, value: any) => {
    if (question.value) {
        store.updateQuestion(question.value.id, { [key]: value });
    }
};
</script>

<template>
    <div class="w-80 border-l bg-card flex flex-col h-full overflow-y-auto">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-sm uppercase tracking-wider text-muted-foreground">Propriétés</h2>
        </div>

        <div v-if="question" class="p-4 space-y-6">
            <div class="space-y-4">
                <div class="space-y-2">
                    <Label for="q-label">Label (Texte de la question)</Label>
                    <Input 
                        id="q-label" 
                        :model-value="question.label" 
                        @update:model-value="v => update('label', v)"
                    />
                </div>

                <div class="space-y-2">
                    <Label for="q-name">Nom technique (XLSForm name)</Label>
                    <Input 
                        id="q-name" 
                        :model-value="question.name" 
                        @update:model-value="v => update('name', v)"
                    />
                    <p class="text-[10px] text-muted-foreground">Unique, sans espaces ni caractères spéciaux.</p>
                </div>

                <div class="space-y-2">
                    <Label for="q-hint">Indice (Hint)</Label>
                    <Input 
                        id="q-hint" 
                        :model-value="question.hint" 
                        @update:model-value="v => update('hint', v)"
                    />
                </div>
            </div>

            <Separator />

            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <Checkbox 
                        id="q-required" 
                        :checked="question.required" 
                        @update:checked="v => update('required', v)"
                    />
                    <Label for="q-required" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Réponse obligatoire
                    </Label>
                </div>
            </div>

            <Separator />

            <div class="space-y-4">
                <h3 class="text-sm font-medium">Logique</h3>
                <div class="space-y-2">
                    <Label for="q-relevant">Condition d'affichage (Relevant)</Label>
                    <Input 
                        id="q-relevant" 
                        :model-value="question.relevant" 
                        @update:model-value="v => update('relevant', v)"
                        placeholder="ex: ${age} > 18"
                    />
                </div>
            </div>
        </div>

        <div v-else class="flex-1 flex flex-col items-center justify-center p-8 text-center text-muted-foreground">
            <p class="text-sm">Sélectionnez une question pour modifier ses propriétés.</p>
        </div>
    </div>
</template>

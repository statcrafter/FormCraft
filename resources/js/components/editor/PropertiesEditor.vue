<script setup lang="ts">
import { useFormEditorStore } from '@/stores/formEditor';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';
import { Plus, X, GripVertical } from 'lucide-vue-next';
import draggable from 'vuedraggable';

import { watch } from 'vue';

const store = useFormEditorStore();

const question = computed(() => store.selectedQuestion);

const nameError = computed(() => {
    if (!question.value) return null;
    if (store.isNameTaken(question.value.name, question.value.id)) {
        return "Ce nom technique est déjà utilisé ailleurs.";
    }
    if (!/^[a-z][a-z0-9_]*$/.test(question.value.name)) {
        return "Doit commencer par une lettre et ne contenir que minuscules, chiffres et _";
    }
    return null;
});

// S'assurer que tous les choix ont un UUID stable dans le store
watch(() => question.value?.id, () => {
    if (question.value?.choices) {
        const choices = question.value.choices;
        const needsFix = choices.some(c => !c.uuid);
        if (needsFix) {
            const fixedChoices = choices.map(c => ({
                ...c,
                uuid: c.uuid || Math.random().toString(36).substring(7)
            }));
            store.updateQuestion(question.value.id, { choices: fixedChoices });
        }
    }
}, { immediate: true });

const update = (key: string, value: any) => {
    if (question.value) {
        store.updateQuestion(question.value.id, { [key]: value });
    }
};

const addChoice = () => {
    if (question.value && question.value.choices) {
        const newChoices = [...question.value.choices];
        const count = newChoices.length + 1;
        newChoices.push({ 
            uuid: Math.random().toString(36).substring(7),
            name: `option_${count}`, 
            label: `Option ${count}` 
        });
        update('choices', newChoices);
    }
};

const removeChoice = (index: number) => {
    if (question.value && question.value.choices) {
        const newChoices = [...question.value.choices];
        newChoices.splice(index, 1);
        update('choices', newChoices);
    }
};

const choicesList = computed({
    get: () => question.value?.choices || [],
    set: (newChoices) => {
        if (question.value) {
            store.updateQuestion(question.value.id, { choices: [...newChoices] });
        }
    }
});

const updateChoice = (index: number, field: 'name' | 'label', value: string) => {
    if (question.value && question.value.choices) {
        const newChoices = [...question.value.choices];
        newChoices[index] = { ...newChoices[index], [field]: value };
        // On met à jour le store, mais on ne déclenche pas forcément un re-render complet qui tue le focus
        // Pinia est intelligent, mais vue-draggable est sensible.
        store.updateQuestion(question.value.id, { choices: newChoices });
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
                    <Label for="q-name" :class="nameError ? 'text-destructive' : ''">Nom technique (XLSForm name)</Label>
                    <Input 
                        id="q-name" 
                        :model-value="question.name" 
                        @update:model-value="v => update('name', v)"
                        :class="nameError ? 'border-destructive' : ''"
                    />
                    <p v-if="nameError" class="text-[10px] text-destructive">{{ nameError }}</p>
                    <p v-else class="text-[10px] text-muted-foreground">Unique, sans espaces ni caractères spéciaux.</p>
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

            <!-- Configuration Répétition -->
            <div v-if="question.type === 'begin_repeat'" class="space-y-4">
                <Separator />
                <h3 class="text-sm font-medium text-primary">Configuration Répétition</h3>
                <div class="space-y-2">
                    <Label for="q-repeat-count">Nombre de répétitions (Fixe ou Dynamique)</Label>
                    <Input 
                        id="q-repeat-count" 
                        :model-value="question.properties.repeat_count" 
                        @update:model-value="v => update('properties', { ...question.properties, repeat_count: v })"
                        placeholder="ex: 5 ou ${nombre_enfants}"
                    />
                    <p class="text-[10px] text-muted-foreground">Laissez vide pour une répétition infinie contrôlée par l'utilisateur.</p>
                </div>
            </div>

            <Separator />

            <!-- Section Choix pour Select -->
            <div v-if="['select_one', 'select_multiple', 'rank'].includes(question.type)" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium">Options de réponse</h3>
                    <Button v-if="question.choices" variant="ghost" size="sm" @click="addChoice" class="h-8 px-2">
                        <Plus class="w-3 h-3 mr-1" /> Ajouter
                    </Button>
                </div>
                
                <div v-if="!question.choices" class="p-4 border border-dashed rounded-md text-center space-y-2">
                    <p class="text-xs text-muted-foreground">Cette question n'a pas encore d'options.</p>
                    <Button size="sm" variant="outline" @click="update('choices', [{uuid: Math.random().toString(36).substring(7), name: 'option_1', label: 'Option 1'}])">
                        Initialiser les options
                    </Button>
                </div>

                <draggable 
                    v-else
                    v-model="choicesList" 
                    item-key="uuid"
                    handle=".handle"
                    class="space-y-2"
                    :animation="200"
                >
                    <template #item="{ element, index }">
                        <div class="flex items-start gap-2 p-2 bg-muted/30 rounded-md border group">
                            <GripVertical class="handle w-4 h-4 mt-1.5 text-muted-foreground cursor-grab opacity-50 group-hover:opacity-100" />
                            <div class="flex-1 space-y-1">
                                <Input 
                                    :model-value="element.label"
                                    @update:model-value="v => updateChoice(index, 'label', v as string)"
                                    class="h-7 text-sm" 
                                    placeholder="Libellé" 
                                />
                                <div class="flex gap-2">
                                    <Input 
                                        :model-value="element.name"
                                        @update:model-value="v => updateChoice(index, 'name', v as string)"
                                        class="h-5 text-[10px] font-mono text-muted-foreground border-none bg-transparent px-0 focus-visible:ring-0" 
                                        placeholder="valeur_interne" 
                                    />
                                    <Input 
                                        :model-value="(element as any).filter_value"
                                        @update:model-value="v => updateChoice(index, 'filter_value' as any, v as string)"
                                        class="h-5 text-[10px] font-mono text-primary/70 border-none bg-transparent px-0 focus-visible:ring-0 text-right" 
                                        placeholder="attribut_filtre" 
                                    />
                                </div>
                            </div>
                            <Button 
                                variant="ghost" 
                                size="icon" 
                                class="h-6 w-6 text-muted-foreground hover:text-destructive opacity-50 group-hover:opacity-100"
                                @click="removeChoice(index)"
                            >
                                <X class="w-3 h-3" />
                            </Button>
                        </div>
                    </template>
                </draggable>

                <div v-if="question.choices" class="space-y-2 pt-2">
                    <Label class="text-[11px] text-muted-foreground">Filtre de choix (Choice Filter)</Label>
                    <Input 
                        :model-value="question.properties.choice_filter" 
                        @update:model-value="v => update('properties', { ...question.properties, choice_filter: v })"
                        placeholder="ex: province = ${ma_province}"
                        class="h-8 text-xs"
                    />
                </div>
                <Separator />
            </div>

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

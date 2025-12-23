<script setup lang="ts">
import { useFormEditorStore } from '@/stores/formEditor';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { computed, watch, ref } from 'vue';
import { Plus, X, GripVertical, Info, Settings, Eye, Palette, Library } from 'lucide-vue-next';
import draggable from 'vuedraggable';
import LogicBuilder from './LogicBuilder.vue';
import MediaPicker from './MediaPicker.vue';
import GlobalSettings from './GlobalSettings.vue';
import AppearancePicker from './AppearancePicker.vue';

const props = defineProps<{
    form: any;
}>();

const store = useFormEditorStore();
const activeTab = ref('properties');
const showAdvanced = ref(false);

const question = computed(() => store.selectedQuestion);

const nameError = computed(() => {
    if (!question.value) return null;
    if (store.isNameTaken(question.value.name, question.value.id)) {
        return "Ce nom technique est déjà utilisé ailleurs.";
    }
    if (!/^[a-z][a-z0-9_]*$/.test(question.value.name)) {
        return "Doit commencer par une lettre minuscule et ne contenir que minuscules, chiffres et _";
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
    if (question.value) {
        activeTab.value = 'properties';
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

const applyPreset = (preset: string) => {
    if (!question.value) return;
    let choices: any[] = [];
    if (preset === 'yes_no') {
        choices = [
            { uuid: '1', name: 'yes', label: 'Oui' },
            { uuid: '2', name: 'no', label: 'Non' }
        ];
    } else if (preset === 'gender') {
        choices = [
            { uuid: '1', name: 'male', label: 'Homme' },
            { uuid: '2', name: 'female', label: 'Femme' },
            { uuid: '3', name: 'other', label: 'Autre' }
        ];
    }
    update('choices', choices);
};

const choicesList = computed({
    get: () => question.value?.choices || [],
    set: (newChoices) => {
        if (question.value) {
            store.updateQuestion(question.value.id, { choices: [...newChoices] });
        }
    }
});

const updateChoice = (index: number, field: string, value: string) => {
    if (question.value && question.value.choices) {
        const newChoices = [...question.value.choices];
        newChoices[index] = { ...newChoices[index], [field]: value };
        store.updateQuestion(question.value.id, { choices: newChoices });
    }
};
</script>

<template>
    <div class="w-80 border-l bg-card flex flex-col h-full overflow-hidden shadow-xl">
        <!-- Onglets -->
        <div class="flex border-b shrink-0 bg-muted/20">
            <button 
                @click="activeTab = 'properties'"
                :class="['flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-all', activeTab === 'properties' ? 'border-b-2 border-primary text-primary bg-background' : 'text-muted-foreground hover:bg-muted']"
            >
                Contenu
            </button>
            <button 
                @click="activeTab = 'settings'"
                :class="['flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-all', activeTab === 'settings' ? 'border-b-2 border-primary text-primary bg-background' : 'text-muted-foreground hover:bg-muted']"
            >
                Global
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div v-if="activeTab === 'properties'">
                <div v-if="question" class="p-4 space-y-6 pb-20 animate-in fade-in slide-in-from-right-2 duration-200">
                    
                    <!-- Section Titre & Saisie -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="q-label" class="text-xs font-bold text-primary flex items-center gap-2">
                                <Info class="w-3 h-3" /> Libellé de la question
                            </Label>
                            <Input 
                                id="q-label" 
                                :model-value="question.label" 
                                @update:model-value="v => update('label', v)"
                                placeholder="Tapez votre question ici..."
                                class="text-sm font-medium"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="q-hint" class="text-[11px] text-muted-foreground flex items-center gap-2">
                                <Eye class="w-3 h-3" /> Instruction d'aide (optionnel)
                            </Label>
                            <Input 
                                id="q-hint" 
                                :model-value="question.hint" 
                                @update:model-value="v => update('hint', v)"
                                placeholder="ex: Cochez une seule case"
                                class="h-8 text-xs"
                            />
                        </div>
                    </div>

                    <Separator />

                    <!-- Style Visuel -->
                    <div class="space-y-3">
                        <Label class="text-xs font-bold text-primary flex items-center gap-2">
                            <Palette class="w-3 h-3" /> Style d'affichage
                        </Label>
                        <AppearancePicker 
                            :model-value="question.appearance" 
                            @update:model-value="v => update('appearance', v)"
                            :type="question.type"
                        />
                    </div>

                    <Separator />

                    <!-- Section Choix pour Select -->
                    <div v-if="['select_one', 'select_multiple', 'rank'].includes(question.type)" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label class="text-xs font-bold text-primary flex items-center gap-2">
                                <ListVideo class="w-3 h-3" /> Options de réponse
                            </Label>
                            <Button v-if="question.choices" variant="ghost" size="sm" @click="addChoice" class="h-7 px-2 text-[10px]">
                                <Plus class="w-3 h-3 mr-1" /> Ajouter
                            </Button>
                        </div>

                        <!-- Presets de bibliothèque -->
                        <div class="flex gap-1 overflow-x-auto pb-1 no-scrollbar">
                            <Button variant="outline" size="sm" class="h-6 px-2 text-[9px] shrink-0" @click="applyPreset('yes_no')">
                                <Library class="w-3 h-3 mr-1" /> Oui/Non
                            </Button>
                            <Button variant="outline" size="sm" class="h-6 px-2 text-[9px] shrink-0" @click="applyPreset('gender')">
                                <Library class="w-3 h-3 mr-1" /> Genre
                            </Button>
                        </div>
                        
                        <div v-if="!question.choices" class="p-4 border border-dashed rounded-md text-center space-y-2 bg-muted/10">
                            <p class="text-[10px] text-muted-foreground italic">Aucune option définie.</p>
                            <Button size="sm" variant="outline" class="h-7 text-[10px]" @click="update('choices', [{uuid: Math.random().toString(36).substring(7), name: 'option_1', label: 'Option 1'}])">
                                Créer des options
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
                                    <GripVertical class="handle w-4 h-4 mt-1.5 text-muted-foreground cursor-grab opacity-50 group-hover:opacity-100 shrink-0" />
                                    <div class="flex-1 space-y-1 overflow-hidden">
                                        <Input 
                                            :model-value="element.label"
                                            @update:model-value="v => updateChoice(index, 'label', v as string)"
                                            class="h-7 text-xs border-none bg-transparent focus-visible:ring-1" 
                                            placeholder="Libellé" 
                                        />
                                        <div class="flex gap-2">
                                            <Input 
                                                :model-value="element.name"
                                                @update:model-value="v => updateChoice(index, 'name', v as string)"
                                                class="h-4 text-[9px] font-mono text-muted-foreground border-none bg-transparent px-0 focus-visible:ring-0" 
                                                placeholder="valeur" 
                                            />
                                        </div>
                                    </div>
                                    <Button 
                                        variant="ghost" 
                                        size="icon" 
                                        class="h-6 w-6 text-muted-foreground hover:text-destructive opacity-0 group-hover:opacity-100"
                                        @click="removeChoice(index)"
                                    >
                                        <X class="w-3 h-3" />
                                    </Button>
                                </div>
                            </template>
                        </draggable>
                    </div>

                    <Separator />

                    <!-- Médias -->
                    <div class="space-y-4">
                        <Label class="text-xs font-bold text-primary flex items-center gap-2">
                            <ImageIcon class="w-3 h-3" /> Illustrer avec un média
                        </Label>
                        <div class="space-y-3">
                            <div class="space-y-1">
                                <MediaPicker 
                                    :model-value="question.media_image" 
                                    @update:model-value="v => update('media_image', v)"
                                    type="image" 
                                    :form-id="props.form.id"
                                />
                            </div>
                        </div>
                    </div>

                    <Separator />

                    <!-- Visibilité Conditionnelle -->
                    <div class="space-y-4">
                        <Label class="text-xs font-bold text-primary flex items-center gap-2">
                            <Settings class="w-3 h-3" /> Afficher sous condition
                        </Label>
                        <LogicBuilder 
                            :model-value="question.properties.logic_rules || []"
                            @update:model-value="v => update('properties', { ...question.properties, logic_rules: v })"
                            @update:xpath="v => update('relevant', v)"
                        />
                    </div>

                    <Separator />

                    <!-- Paramètres avancés (Techniques) -->
                    <div class="space-y-2">
                        <button 
                            @click="showAdvanced = !showAdvanced"
                            class="flex items-center justify-between w-full text-[10px] uppercase font-bold text-muted-foreground hover:text-primary transition-colors"
                        >
                            Paramètres techniques
                            <Settings :class="['w-3 h-3 transition-transform', showAdvanced ? 'rotate-90' : '']" />
                        </button>

                        <div v-if="showAdvanced" class="space-y-4 pt-2 animate-in slide-in-from-top-2 duration-200">
                            <div class="space-y-2">
                                <Label for="q-name" :class="['text-[10px]', nameError ? 'text-destructive' : '']">Nom technique (XLSForm name)</Label>
                                <Input 
                                    id="q-name" 
                                    :model-value="question.name" 
                                    @update:model-value="v => update('name', v)"
                                    :class="['h-7 text-xs font-mono', nameError ? 'border-destructive' : '']"
                                />
                                <p v-if="nameError" class="text-[9px] text-destructive">{{ nameError }}</p>
                            </div>

                            <div class="flex items-center space-x-2">
                                <Checkbox 
                                    id="q-required" 
                                    :checked="question.required" 
                                    @update:checked="v => update('required', v)"
                                />
                                <Label for="q-required" class="text-[11px]">Obligatoire (Required)</Label>
                            </div>

                            <div v-if="question.relevant" class="p-2 bg-muted/50 rounded text-[9px] font-mono break-all text-muted-foreground border">
                                XPath : {{ question.relevant }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="flex-1 flex flex-col items-center justify-center p-8 text-center text-muted-foreground h-full min-h-[400px]">
                    <div class="p-4 rounded-full bg-muted/30 mb-4">
                        <Eye class="w-8 h-8 opacity-20" />
                    </div>
                    <p class="text-sm">Cliquez sur une question au milieu pour la modifier.</p>
                </div>
            </div>

            <!-- Onglet Réglages -->
            <div v-else class="animate-in fade-in slide-in-from-left-2 duration-200 h-full">
                <GlobalSettings />
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

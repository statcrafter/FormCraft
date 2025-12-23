<script setup lang="ts">
import { useFormEditorStore } from '@/stores/formEditor';
import { Button } from '@/components/ui/button';
import { Plus, Trash2 } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { computed, watch, ref } from 'vue';

const props = defineProps<{
    modelValue: any[];
    logicOperator?: string;
}>();

const emit = defineEmits(['update:modelValue', 'update:xpath', 'update:logicOperator']);
const store = useFormEditorStore();

const rules = ref<any[]>(props.modelValue || []);
const currentOperator = ref(props.logicOperator || 'and');

const operators = [
    { label: 'est égal à', value: '=' },
    { label: 'est différent de', value: '!=' },
    { label: 'est supérieur à', value: '>' },
    { label: 'est inférieur à', value: '<' },
    { label: 'est supérieur ou égal à', value: '>=' },
    { label: 'est inférieur ou égal à', value: '<=' },
    { label: 'contient', value: 'selected' },
];

const addRule = () => {
    rules.value.push({
        field: '',
        operator: '=',
        value: ''
    });
};

const removeRule = (index: number) => {
    rules.value.splice(index, 1);
};

const generateXPath = (rulesList: any[], operator: string) => {
    if (rulesList.length === 0) return '';
    
    const parts = rulesList.map(rule => {
        if (!rule.field) return '';
        const fieldRef = `$\{${rule.field}\}`;
        if (rule.operator === 'selected') return `selected(${fieldRef}, '${rule.value}')`;
        const isNumeric = !isNaN(rule.value) && rule.value !== '';
        const val = isNumeric ? rule.value : `'${rule.value}'`;
        return `${fieldRef} ${rule.operator} ${val}`;
    }).filter(r => r !== '');

    if (parts.length === 0) return '';
    return parts.join(` ${operator} `);
};

watch([rules, currentOperator], () => {
    emit('update:modelValue', rules.value);
    emit('update:logicOperator', currentOperator.value);
    emit('update:xpath', generateXPath(rules.value, currentOperator.value));
}, { deep: true });

// Filtrer les questions pour ne pas proposer la question actuelle (évite les références circulaires)
const availableFields = computed(() => {
    return store.allQuestions.filter(q => q.id !== store.selectedQuestionId && q.name);
});
</script>

<template>
    <div class="space-y-3">
        <!-- Sélecteur ET / OU -->
        <div v-if="rules.length > 1" class="flex p-1 bg-muted rounded-lg w-fit">
            <button 
                @click="currentOperator = 'and'"
                :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', currentOperator === 'and' ? 'bg-background shadow-sm text-primary' : 'text-muted-foreground']"
            >
                TOUTES (ET)
            </button>
            <button 
                @click="currentOperator = 'or'"
                :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', currentOperator === 'or' ? 'bg-background shadow-sm text-primary' : 'text-muted-foreground']"
            >
                UNE SEULE (OU)
            </button>
        </div>

        <div v-for="(rule, index) in rules" :key="index" class="p-3 border rounded-md bg-muted/20 space-y-2 relative group animate-in zoom-in-95 duration-200">
            <Button 
                variant="ghost" 
                size="icon" 
                class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-background border shadow-sm opacity-0 group-hover:opacity-100 transition-opacity"
                @click="removeRule(index)"
            >
                <Trash2 class="w-3 h-3 text-destructive" />
            </Button>

            <div class="space-y-2">
                <label class="text-[10px] font-medium uppercase text-muted-foreground">Si la question</label>
                <select 
                    v-model="rule.field"
                    class="w-full h-8 text-xs rounded-md border border-input bg-background px-2 focus:ring-1 focus:ring-primary outline-none"
                >
                    <option value="" disabled>Choisir une question...</option>
                    <option v-for="q in availableFields" :key="q.id" :value="q.name">
                        {{ q.label }} ({{ q.name }})
                    </option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="space-y-1">
                    <select 
                        v-model="rule.operator"
                        class="w-full h-8 text-xs rounded-md border border-input bg-background px-2 focus:ring-1 focus:ring-primary outline-none"
                    >
                        <option v-for="op in operators" :key="op.value" :value="op.value">
                            {{ op.label }}
                        </option>
                    </select>
                </div>
                <div class="space-y-1">
                    <Input 
                        v-model="rule.value" 
                        placeholder="Valeur" 
                        class="h-8 text-xs"
                    />
                </div>
            </div>
        </div>

        <Button variant="outline" size="sm" class="w-full border-dashed" @click="addRule">
            <Plus class="w-3 h-3 mr-2" />
            Ajouter une condition
        </Button>
    </div>
</template>

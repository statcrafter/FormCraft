<script setup lang="ts">
import { useFormEditorStore, type Question } from '@/stores/formEditor';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Smartphone, Tablet, Monitor, Send } from 'lucide-vue-next';

const props = defineProps<{ open: boolean }>();
const emit = defineEmits(['update:open']);
const store = useFormEditorStore();

const formData = ref<Record<string, any>>({});
const previewMode = ref('mobile'); // smartphone, tablet, desktop

// Reset data when opening
watch(() => props.open, (isOpen) => {
    if (isOpen) formData.value = {};
});

const evalRule = (rule: any) => {
    const userVal = formData.value[rule.field];
    if (userVal === undefined || userVal === null || userVal === '') return false;
    
    const targetVal = rule.value;
    
    switch(rule.operator) {
        case '=': return String(userVal) === String(targetVal);
        case '!=': return String(userVal) !== String(targetVal);
        case '>': return Number(userVal) > Number(targetVal);
        case '<': return Number(userVal) < Number(targetVal);
        case '>=': return Number(userVal) >= Number(targetVal);
        case '<=': return Number(userVal) <= Number(targetVal);
        case 'selected': 
            if (Array.isArray(userVal)) return userVal.includes(targetVal);
            return String(userVal) === String(targetVal);
        default: return false;
    }
};

const isVisible = (question: Question): boolean => {
    const rules = question.properties?.logic_rules || [];
    if (rules.length === 0) return true;

    const op = question.properties?.logic_operator || 'and';
    const results = rules.map(evalRule);

    return op === 'and' ? results.every(Boolean) : results.some(Boolean);
};

const getVisibleQuestions = (list: Question[]): Question[] => {
    return list.filter(isVisible);
};
</script>

<template>
    <Dialog :open="open" @update:open="v => emit('update:open', v)">
        <DialogContent class="max-w-5xl h-[90vh] flex flex-col p-0 gap-0 overflow-hidden">
            <DialogHeader class="p-4 border-b shrink-0 flex flex-row items-center justify-between">
                <div>
                    <DialogTitle>Prévisualisation : {{ store.formTitle }}</DialogTitle>
                    <p class="text-xs text-muted-foreground mt-1">Testez le comportement de votre formulaire en temps réel.</p>
                </div>
                
                <div class="flex bg-muted p-1 rounded-lg mr-8">
                    <Button variant="ghost" size="icon" :class="['h-8 w-8', previewMode === 'mobile' ? 'bg-background shadow-sm' : '']" @click="previewMode = 'mobile'">
                        <Smartphone class="w-4 h-4" />
                    </Button>
                    <Button variant="ghost" size="icon" :class="['h-8 w-8', previewMode === 'tablet' ? 'bg-background shadow-sm' : '']" @click="previewMode = 'tablet'">
                        <Tablet class="w-4 h-4" />
                    </Button>
                    <Button variant="ghost" size="icon" :class="['h-8 w-8', previewMode === 'desktop' ? 'bg-background shadow-sm' : '']" @click="previewMode = 'desktop'">
                        <Monitor class="w-4 h-4" />
                    </Button>
                </div>
            </DialogHeader>

            <div class="flex-1 bg-muted/30 overflow-y-auto p-8 flex justify-center items-start">
                <div 
                    :class="[
                        'bg-background shadow-2xl border transition-all duration-300 overflow-y-auto relative',
                        previewMode === 'mobile' ? 'w-[375px] min-h-[667px] rounded-[3rem] border-[8px] border-slate-800 p-8' : '',
                        previewMode === 'tablet' ? 'w-[768px] min-h-[1024px] rounded-xl p-12' : '',
                        previewMode === 'desktop' ? 'w-full max-w-4xl min-h-full rounded-lg p-16' : ''
                    ]"
                >
                    <!-- Form Content -->
                    <div class="space-y-8 pb-20">
                        <div class="border-b pb-4">
                            <h1 class="text-2xl font-bold">{{ store.formTitle }}</h1>
                            <p class="text-sm text-muted-foreground mt-1">Version {{ store.formVersion }}</p>
                        </div>

                        <div v-for="q in getVisibleQuestions(store.questions)" :key="q.id" class="space-y-3 animate-in fade-in slide-in-from-bottom-2">
                            <div class="space-y-1">
                                <Label class="text-base font-semibold">
                                    {{ q.label }}
                                    <span v-if="q.required" class="text-destructive">*</span>
                                </Label>
                                <p v-if="q.hint" class="text-xs text-muted-foreground italic">{{ q.hint }}</p>
                            </div>

                            <div class="pt-1">
                                <!-- Text / Integer / Decimal -->
                                <Input 
                                    v-if="['text', 'integer', 'decimal'].includes(q.type)"
                                    v-model="formData[q.name]"
                                    :type="q.type === 'text' ? 'text' : 'number'"
                                    :placeholder="q.type === 'text' ? 'Votre réponse...' : '0'"
                                />

                                <!-- Select One (Native Radio Implementation) -->
                                <div v-if="q.type === 'select_one'" class="space-y-2">
                                    <div v-for="opt in q.choices" :key="opt.uuid" class="flex items-center space-x-2 border p-3 rounded-md hover:bg-muted/50 transition-colors">
                                        <input 
                                            type="radio" 
                                            :name="q.name" 
                                            :value="opt.name" 
                                            :id="opt.uuid"
                                            v-model="formData[q.name]"
                                            class="h-4 w-4 text-primary border-gray-300 focus:ring-primary"
                                        />
                                        <Label :for="opt.uuid" class="flex-1 cursor-pointer">{{ opt.label }}</Label>
                                    </div>
                                </div>

                                <!-- Select Multiple -->
                                <div v-if="q.type === 'select_multiple'" class="space-y-2">
                                    <div v-for="opt in q.choices" :key="opt.uuid" class="flex items-center space-x-3 border p-3 rounded-md">
                                        <Checkbox 
                                            :id="opt.uuid"
                                            :checked="(formData[q.name] || []).includes(opt.name)"
                                            @update:checked="v => {
                                                const current = formData[q.name] || [];
                                                if(v) formData[q.name] = [...current, opt.name];
                                                else formData[q.name] = current.filter((i:string) => i !== opt.name);
                                            }"
                                        />
                                        <Label :for="opt.uuid" class="flex-1 cursor-pointer font-medium">{{ opt.label }}</Label>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div v-if="q.type === 'note'" class="p-4 bg-primary/5 border border-primary/10 rounded-lg text-sm text-primary/80">
                                    {{ q.label }}
                                </div>

                                <!-- Date / Time -->
                                <Input 
                                    v-if="['date', 'time', 'datetime'].includes(q.type)"
                                    v-model="formData[q.name]"
                                    :type="q.type === 'datetime' ? 'datetime-local' : q.type"
                                />
                            </div>
                        </div>

                        <div class="pt-8 border-t">
                            <Button class="w-full flex gap-2 h-12 text-lg">
                                <Send class="w-5 h-5" />
                                Envoyer les données
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
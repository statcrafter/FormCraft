import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export interface Choice {
    uuid?: string;
    name: string;
    label: string;
}

export interface Question {
    id: string;
    type: string;
    name: string;
    label: string;
    hint?: string;
    required: boolean;
    relevant?: string;
    constraint?: string;
    constraint_message?: string;
    calculation?: string;
    appearance?: string;
    default?: any;
    choices?: Choice[];
    properties: Record<string, any>;
}

export const useFormEditorStore = defineStore('formEditor', () => {
    const questions = ref<Question[]>([]);
    const selectedQuestionId = ref<string | null>(null);
    const formTitle = ref('');
    const formId = ref('');

    const selectedQuestion = computed(() => 
        questions.value.find(q => q.id === selectedQuestionId.value) || null
    );

    function setQuestions(newQuestions: Question[]) {
        questions.value = newQuestions;
    }

    function addQuestion(type: string, index?: number) {
        const uid = Math.random().toString(36).substring(7);
        const newQuestion: Question = {
            id: uid,
            type,
            name: `${type.replace(/[^a-z0-9]/gi, '_')}_${uid}`,
            label: `Nouvelle question (${type})`,
            required: false,
            properties: {},
        };

        if (['select_one', 'select_multiple', 'rank'].includes(type)) {
            newQuestion.choices = [
                { uuid: Math.random().toString(36).substring(7), name: 'option_1', label: 'Option 1' },
                { uuid: Math.random().toString(36).substring(7), name: 'option_2', label: 'Option 2' },
            ];
        }

        if (typeof index === 'number') {
            questions.value.splice(index, 1, newQuestion);
        } else {
            questions.value.push(newQuestion);
        }
        
        selectedQuestionId.value = uid;
    }

    function removeQuestion(id: string) {
        const index = questions.value.findIndex(q => q.id === id);
        if (index !== -1) {
            questions.value.splice(index, 1);
            if (selectedQuestionId.value === id) {
                selectedQuestionId.value = null;
            }
        }
    }

    function selectQuestion(id: string | null) {
        selectedQuestionId.value = id;
    }

    function updateQuestion(id: string, updates: Partial<Question>) {
        const index = questions.value.findIndex(q => q.id === id);
        if (index !== -1) {
            questions.value[index] = { ...questions.value[index], ...updates };
        }
    }

    return { 
        questions, 
        selectedQuestionId, 
        selectedQuestion,
        formTitle,
        formId,
        setQuestions,
        addQuestion, 
        removeQuestion, 
        selectQuestion,
        updateQuestion 
    };
});
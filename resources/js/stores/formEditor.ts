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
    children?: Question[];
    properties: Record<string, any>;
}

export const useFormEditorStore = defineStore('formEditor', () => {
    const questions = ref<Question[]>([]);
    const selectedQuestionId = ref<string | null>(null);
    const formTitle = ref('');
    const formId = ref('');

    // Recherche récursive d'une question par ID
    function findQuestionById(list: Question[], id: string): Question | null {
        for (const q of list) {
            if (q.id === id) return q;
            if (q.children) {
                const found = findQuestionById(q.children, id);
                if (found) return found;
            }
        }
        return null;
    }

    const selectedQuestion = computed(() => {
        if (!selectedQuestionId.value) return null;
        return findQuestionById(questions.value, selectedQuestionId.value);
    });

    function setQuestions(newQuestions: Question[]) {
        questions.value = newQuestions;
    }

    // Récupérer tous les noms utilisés récursivement
    function getAllNames(list: Question[]): string[] {
        let names: string[] = [];
        for (const q of list) {
            names.push(q.name);
            if (q.children) {
                names = [...names, ...getAllNames(q.children)];
            }
        }
        return names;
    }

    // Générer un nom unique
    function getUniqueName(baseName: string): string {
        const allNames = getAllNames(questions.value);
        let name = baseName;
        let counter = 1;
        while (allNames.includes(name)) {
            name = `${baseName}_${counter}`;
            counter++;
        }
        return name;
    }

    // Vérifier si un nom existe déjà (pour la validation manuelle)
    function isNameTaken(name: string, excludeId: string): boolean {
        function check(list: Question[]): boolean {
            for (const q of list) {
                if (q.name === name && q.id !== excludeId) return true;
                if (q.children) {
                    if (check(q.children)) return true;
                }
            }
            return false;
        }
        return check(questions.value);
    }

    function addQuestion(type: string, parentId?: string) {
        const uid = Math.random().toString(36).substring(7);
        const baseName = `${type.replace(/[^a-z0-9]/gi, '_')}`;
        // Assurer l'unicité du nom dès la création
        const uniqueName = getUniqueName(`${baseName}_${uid}`);

        const newQuestion: Question = {
            id: uid,
            type,
            name: uniqueName,
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

        if (['begin_group', 'begin_repeat'].includes(type)) {
            newQuestion.children = [];
        }

        if (parentId) {
            const parent = findQuestionById(questions.value, parentId);
            if (parent && parent.children) {
                parent.children.push(newQuestion);
            }
        } else {
            questions.value.push(newQuestion);
        }
        
        selectedQuestionId.value = uid;
    }

    function removeQuestionRecursive(list: Question[], id: string): boolean {
        const index = list.findIndex(q => q.id === id);
        if (index !== -1) {
            list.splice(index, 1);
            return true;
        }
        for (const q of list) {
            if (q.children) {
                if (removeQuestionRecursive(q.children, id)) return true;
            }
        }
        return false;
    }

    function removeQuestion(id: string) {
        removeQuestionRecursive(questions.value, id);
        if (selectedQuestionId.value === id) {
            selectedQuestionId.value = null;
        }
    }

    function selectQuestion(id: string | null) {
        selectedQuestionId.value = id;
    }

    function updateQuestion(id: string, updates: Partial<Question>) {
        const question = findQuestionById(questions.value, id);
        if (question) {
            Object.assign(question, updates);
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
        updateQuestion,
        isNameTaken
    };
});

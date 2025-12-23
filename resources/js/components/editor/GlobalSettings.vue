<script setup lang="ts">
import { useFormEditorStore } from '@/stores/formEditor';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';

const store = useFormEditorStore();
</script>

<template>
    <div class="p-4 space-y-6">
        <div class="space-y-4">
            <h3 class="text-sm font-medium text-primary uppercase tracking-wider">Identité technique</h3>
            <div class="space-y-2">
                <Label for="s-id" class="text-xs">ID technique (form_id)</Label>
                <Input id="s-id" v-model="store.formId" class="h-8 text-xs font-mono" />
                <p class="text-[10px] text-muted-foreground italic">L'identifiant unique utilisé par le serveur de collecte.</p>
            </div>
            <div class="space-y-2">
                <Label for="s-version" class="text-xs">Version</Label>
                <Input id="s-version" v-model="store.formVersion" class="h-8 text-xs font-mono" />
                <p class="text-[10px] text-muted-foreground italic">Incrémentez ce nombre pour forcer la mise à jour sur les mobiles.</p>
            </div>
        </div>

        <Separator />

        <div class="space-y-4">
            <h3 class="text-sm font-medium text-primary uppercase tracking-wider">Interface & Collecte</h3>
            <div class="space-y-2">
                <Label for="s-style" class="text-xs">Style d'affichage</Label>
                <select 
                    v-model="store.globalSettings.style" 
                    class="w-full h-8 rounded-md border border-input bg-background px-2 text-xs focus:ring-1 focus:ring-primary outline-none"
                >
                    <option value="pages">Pages (Une question par écran)</option>
                    <option value="theme-grid">Grille (Optimisé tablette)</option>
                    <option value="">Standard (Liste défilante)</option>
                </select>
            </div>
            <div class="space-y-2">
                <Label for="s-instance" class="text-xs">Nom des instances (instance_name)</Label>
                <Input 
                    id="s-instance" 
                    v-model="store.globalSettings.instance_name" 
                    placeholder="ex: concat(${nom}, '_', ${date})" 
                    class="h-8 text-xs"
                />
                <p class="text-[10px] text-muted-foreground italic">Définit le titre affiché dans la liste des formulaires remplis.</p>
            </div>
            <div class="space-y-2">
                <Label for="s-url" class="text-xs">URL de soumission</Label>
                <Input 
                    id="s-url" 
                    v-model="store.globalSettings.submission_url" 
                    placeholder="https://votre-serveur.com/submission" 
                    class="h-8 text-xs"
                />
            </div>
        </div>
    </div>
</template>

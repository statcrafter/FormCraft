<?php

namespace App\Services;

use Illuminate\Support\Str;

class XlsFormImporter
{
    protected array $choices = [];
    protected array $settings = [];
    protected array $survey = [];

    public function import(array $sheets): array
    {
        // 1. Charger les settings
        $this->settings = $this->parseSettings($sheets['settings'] ?? []);

        // 2. Charger les choix
        $this->choices = $this->parseChoices($sheets['choices'] ?? []);

        // 3. Construire l'arbre des questions
        $questions = $this->buildQuestionTree($sheets['survey'] ?? []);

        return [
            'title' => $this->settings['form_title'] ?? 'Formulaire importé',
            'form_id' => $this->settings['form_id'] ?? 'imported_' . Str::random(5),
            'version' => (string) ($this->settings['version'] ?? '1'),
            'definition' => $questions,
            'settings' => $this->settings,
        ];
    }

    protected function parseSettings(array $rows): array
    {
        if (empty($rows)) return [];
        
        // On prend la première ligne de données (après les headers)
        // Mais Maatwebsite peut renvoyer les données différemment selon la config.
        // On suppose ici que $rows est un tableau associatif ou indexé.
        $headers = array_shift($rows);
        $data = array_shift($rows);

        if (!$headers || !$data) return [];

        return array_combine($headers, array_pad($data, count($headers), ''));
    }

    protected function parseChoices(array $rows): array
    {
        $headers = array_shift($rows);
        if (!$headers) return [];

        $choicesByList = [];
        foreach ($rows as $row) {
            $choice = array_combine($headers, array_pad($row, count($headers), ''));
            $listName = $choice['list_name'] ?? null;
            if ($listName) {
                $choicesByList[$listName][] = [
                    'uuid' => Str::random(8),
                    'name' => (string) $choice['name'],
                    'label' => (string) ($choice['label'] ?? $choice['label::French (fr)'] ?? $choice['name']),
                ];
            }
        }
        return $choicesByList;
    }

    protected function buildQuestionTree(array $rows): array
    {
        $headers = array_shift($rows);
        if (!$headers) return [];

        $root = [];
        $stack = [&$root];

        foreach ($rows as $row) {
            if (empty($row) || !isset($row[0])) continue;
            
            $qRaw = array_combine($headers, array_pad($row, count($headers), ''));
            $type = $qRaw['type'] ?? '';

            if (Str::startsWith($type, 'end_')) {
                array_pop($stack);
                continue;
            }

            // Extraction du type et de la liste de choix si applicable
            $cleanType = $type;
            $listName = null;
            if (Str::contains($type, ' ')) {
                [$cleanType, $listName] = explode(' ', $type, 2);
            }

            $question = [
                'id' => Str::random(8),
                'type' => $cleanType,
                'name' => $qRaw['name'] ?? 'q_' . Str::random(5),
                'label' => $qRaw['label'] ?? $qRaw['label::French (fr)'] ?? '',
                'hint' => $qRaw['hint'] ?? '',
                'required' => ($qRaw['required'] ?? '') === 'yes',
                'relevant' => $qRaw['relevant'] ?? '',
                'constraint' => $qRaw['constraint'] ?? '',
                'constraint_message' => $qRaw['constraint_message'] ?? '',
                'calculation' => $qRaw['calculation'] ?? '',
                'media_image' => $qRaw['media::image'] ?? '',
                'media_audio' => $qRaw['media::audio'] ?? '',
                'properties' => [
                    'repeat_count' => $qRaw['repeat_count'] ?? '',
                    'choice_filter' => $qRaw['choice_filter'] ?? '',
                ],
            ];

            // Attacher les choix
            if ($listName && isset($this->choices[$listName])) {
                $question['choices'] = $this->choices[$listName];
            }

            // Gérer l'imbrication
            if (Str::startsWith($cleanType, 'begin_')) {
                $question['children'] = [];
                $currentLevel = &$stack[count($stack) - 1];
                $currentLevel[] = $question;
                $stack[] = &$currentLevel[count($currentLevel) - 1]['children'];
            } else {
                $currentLevel = &$stack[count($stack) - 1];
                $currentLevel[] = $question;
            }
        }

        return $root;
    }
}

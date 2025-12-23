<?php

namespace App\Exports;

use App\Models\Form;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class XlsFormExport implements WithMultipleSheets
{
    protected Form $form;
    protected array $surveyRows = [];
    protected array $choicesRows = [];

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function sheets(): array
    {
        $this->processQuestions($this->form->definition ?? []);

        return [
            new GenericSheet('survey', $this->getSurveyHeadings(), $this->surveyRows),
            new GenericSheet('choices', ['list_name', 'name', 'label'], $this->choicesRows),
            new GenericSheet('settings', ['form_title', 'form_id', 'version', 'default_language', 'style', 'submission_url', 'instance_name'], [
                [
                    $this->form->settings['form_title'] ?? $this->form->title,
                    $this->form->form_id,
                    $this->form->version,
                    $this->form->settings['default_language'] ?? 'French (fr)',
                    $this->form->settings['style'] ?? 'pages',
                    $this->form->settings['submission_url'] ?? '',
                    $this->form->settings['instance_name'] ?? '',
                ]
            ]),
        ];
    }

    protected function processQuestions(array $questions)
    {
        foreach ($questions as $q) {
            $type = $q['type'];
            
            // Gestion des types avec listes de choix
            if (isset($q['choices']) && !empty($q['choices'])) {
                $listName = $q['name'] . '_list';
                $type = $q['type'] . ' ' . $listName;
                
                foreach ($q['choices'] as $choice) {
                    $this->choicesRows[] = [
                        $listName,
                        $choice['name'],
                        $choice['label']
                    ];
                }
            }

            // Ligne de la question
            $this->surveyRows[] = [
                $type,
                $q['name'],
                $q['label'],
                $q['hint'] ?? '',
                ($q['required'] ?? false) ? 'yes' : 'no',
                $q['relevant'] ?? '',
                $q['constraint'] ?? '',
                $q['constraint_message'] ?? '',
                $q['calculation'] ?? '',
                $q['properties']['repeat_count'] ?? '',
                $q['properties']['choice_filter'] ?? '',
                $q['media_image'] ?? '',
                $q['media_audio'] ?? '',
                $q['media_video'] ?? '',
            ];

            // Si c'est un groupe ou une répétition, on traite les enfants et on ferme
            if (isset($q['children']) && is_array($q['children'])) {
                $this->processQuestions($q['children']);
                
                $endType = str_replace('begin_', 'end_', $q['type']);
                $this->surveyRows[] = array_fill(0, count($this->getSurveyHeadings()), '');
                $this->surveyRows[count($this->surveyRows)-1][0] = $endType;
            }
        }
    }

    protected function getSurveyHeadings(): array
    {
        return [
            'type', 
            'name', 
            'label', 
            'hint', 
            'required', 
            'relevant', 
            'constraint', 
            'constraint_message', 
            'calculation', 
            'repeat_count', 
            'choice_filter',
            'media::image',
            'media::audio',
            'media::video'
        ];
    }
}

/**
 * Classe utilitaire interne pour les feuilles individuelles
 */
class GenericSheet implements FromArray, WithTitle, WithHeadings
{
    private string $title;
    private array $headings;
    private array $data;

    public function __construct(string $title, array $headings, array $data)
    {
        $this->title = $title;
        $this->headings = $headings;
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
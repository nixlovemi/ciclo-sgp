<?php

namespace App\Presenters;

use App\Models\Job;

final class JobRegisterPresenter
{
    private const FIELD_OBJECTIVE = 'objective';
    private const FIELD_MATERIAL = 'material';
    private const FIELD_TECHNICAL = 'technical';
    private const FIELD_CONTENT_INFO = 'content_info';
    private const FIELD_CREATIVE_DETAILS = 'creative_details';
    private const FIELD_DELIVERABLES = 'deliverables';
    private const FIELD_NOTES = 'notes';

    public static function getJobBriefingFields(Job $Job): array
    {
        $fields = [
            self::FIELD_OBJECTIVE => [
                'label' => 'Breve Descrição do Job:',
                'name' => 'job-b-objectvie',
            ],
            self::FIELD_MATERIAL => [
                'label' => 'Uso do Material:',
                'name' => 'job-b-material',
            ],
            self::FIELD_TECHNICAL => [
                'label' => 'Informações Técnicas:',
                'name' => 'job-b-technical',
            ],
            self::FIELD_CONTENT_INFO => [
                'label' => 'Mensagem e Informações de Conteúdo:',
                'name' => 'job-b-content-info',
            ],
            self::FIELD_CREATIVE_DETAILS => [
                'label' => 'Conceito Criativo / Identidade do Job:',
                'name' => 'job-b-creative-det',
            ],
            self::FIELD_DELIVERABLES => [
                'label' => 'Entregáveis:',
                'name' => 'job-b-deliverables',
            ],
            self::FIELD_NOTES => [
                'label' => 'Observações:',
                'name' => 'job-b-notes',
            ],
        ];

        if ($Job->created_at > \Carbon\Carbon::createFromFormat('Y-m-d', '2026-02-16')) {
            // changes based on a request to keep only 3 fields
            unset($fields[self::FIELD_MATERIAL]);
            unset($fields[self::FIELD_TECHNICAL]);
            unset($fields[self::FIELD_CONTENT_INFO]);
            unset($fields[self::FIELD_NOTES]);

            // adjust labels for the remaining fields
            $fields[self::FIELD_OBJECTIVE]['label'] = 'Descrição do Projeto (Briefing):';
            $fields[self::FIELD_CREATIVE_DETAILS]['label'] = 'Especificações Técnicas:';
            $fields[self::FIELD_DELIVERABLES]['label'] = 'Itens a Entregar:';
        }

        return $fields;
    }
}

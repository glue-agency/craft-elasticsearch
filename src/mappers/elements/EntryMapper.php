<?php

namespace GlueAgency\Elasticsearch\mappers\elements;

use craft\base\Element;
use GlueAgency\Elasticsearch\factories\FieldMappingFactory;
use GlueAgency\Elasticsearch\helpers\FieldHelper;

class EntryMapper implements ElementMapperInterface
{

    public function format(Element $element): array
    {
        $data = [
            'title'        => $element->title,
            'slug'         => $element->slug,
            'url'          => $element->getUrl(),
            'enabled'      => $element->getEnabledForSite(),
            'site'         => [
                'id'       => $element->site->id,
                'language' => $element->site->language,
            ],
            'section'      => [
                'id'     => $element->section->id,
                'handle' => $element->section->handle,
            ],
            'entry_type'   => [
                'id'     => $element->type->id,
                'handle' => $element->type->handle,
            ],
            'post_date'    => $element->postDate?->format('c'),
            'expiry_date'  => $element->expiryDate?->format('c'),
            'date_updated' => $element->dateUpdated->format('c'),
            'date_created' => $element->dateCreated->format('c'),
        ];

        $fieldMappingFactory = new FieldMappingFactory;
        foreach($element->getFieldLayout()->getCustomFields() as $field) {
            $handle = FieldHelper::toElasticSafeName($field);

            $data[$handle] = $fieldMappingFactory->format($element, $field);
        }

        return $data;
    }
}

<?php

namespace AdminBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;

class AdminExtension extends \Twig_Extension
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('htmlAttributes', [
                $this,
                'htmlAttributesFilter'
            ]),
            new \Twig_SimpleFilter('propertyValue', [
                $this, 'propertyValue'
            ],
            [
                'needs_environment' => true,
                'is_safe' => ['html']
            ])
        ];
    }

    public function htmlAttributes(array $attributes)
    {
        $attr = '';
        foreach ($attribtes as $key => $value)
        {
            $attr .= ' ' . $key . '"=' . htmlspecialchars($value) . '"';
        }

        return $attr;
    }

    public function propertyValue(\Twig_Environment $environment, $property, array $options = null)
    {
        switch (gettype($property)) {
            case 'string':
                return htmlspecialchars($property);
            case 'object':
                if ($property instanceof \DateTime) {
                    $formatter = new \IntlDateFormatter($this->requestStack->getCurrentRequest()->getLocale(), \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
                    $formatter->setPattern(array_key_exists('dateTimeFormat', $options) ? $options['dateTimeFormat'] : 'd. MMMM Y H:mm');
                    return $formatter->format($property);
                } else {
                    return $environment->render('AdminBundle::_partial/entity_link.html.twig', [
                        'entity' => $property
                    ]);
                }
            case 'boolean':
                return $property ? '×' : '✓';
            case 'array':
                $result = '';
                foreach ($property as $value)
                {
                    if ($result !== '') {
                        $result .= ', ';
                    }

                    $result .= $value;
                }
                return $result;
        }

        return $property;
    }
}

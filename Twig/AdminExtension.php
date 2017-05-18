<?php

namespace AdminBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AdminExtension extends \Twig_Extension
{
    protected $requestStack;
    protected $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
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
            ]),
            new \Twig_SimpleFilter('transEntity', [
                $this,
                'transEntity'
            ])
        ];
    }

    public function transEntity($entity)
    {
        $string = $entity->__toString();
        $options = [];
        preg_match_all('/%\w+%/', $string, $matches);
        if (count($matches) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            foreach ($matches[0] as $match)
            {
                $property = str_replace('%', '', $match);
                if (!array_key_exists($match, $options)) {
                    $options[$match] = $accessor->getValue($entity, $property);
                }
            }
        }

        return $this->translator->trans($string, $options);
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

    public function propertyValue(\Twig_Environment $environment, $property, $options = null)
    {
        switch (gettype($property)) {
            case 'string':
                return htmlspecialchars($property);
            case 'object':
                if ($property instanceof \DateTime) {
                    $formatter = new \IntlDateFormatter($this->requestStack->getCurrentRequest()->getLocale(), \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
                    $formatter->setPattern(is_array($options) && array_key_exists('dateTimeFormat', $options) ? $options['dateTimeFormat'] : 'd. MMMM Y H:mm');
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

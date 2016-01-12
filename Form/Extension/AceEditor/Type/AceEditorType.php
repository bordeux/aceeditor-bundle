<?php

/**
 * This file is part of the AceEditorBundle.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Norzechowicz\AceEditorBundle\Form\Extension\AceEditor\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AceEditorType
 *
 * @author Norbert Orzechowicz <norbert@orzechowicz.pl>
 */
class AceEditorType extends AbstractType
{
    private $defaultUnit = 'px';
    private $units = array('%', 'in', 'cm', 'mm', 'em', 'ex', 'pt', 'pc', 'px');

    /**
     * Add the image_path option
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'wrapper_attr' => array(),
            'width' => 200,
            'height' => 200,
            'font_size' => 12,
            'mode' => 'ace/mode/html',
            'theme' => 'ace/theme/monokai',
            'tab_size' => null,
            'read_only' => null,
            'use_soft_tabs' => null,
            'use_wrap_mode' => null,
            'show_print_margin' => null,
            'highlight_active_line' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('font_size', 'height', 'highlight_active_line', 'mode', 'tab_size',
            'show_print_margin', 'use_wrap_mode', 'theme', 'width', 'wrapper_attr', 'use_soft_tabs', 'read_only'));

        $wrapperAttrNormalizer = function (Options $options, $aceAttr) {
            if (is_array($aceAttr)) {
                if (array_key_exists('id', $aceAttr)) {
                    unset($aceAttr['id']);
                }
            } else {
                $aceAttr = array();
            }

            return $aceAttr;
        };

        $defaultUnit = $this->defaultUnit;
        $allowedUnits = $this->units;
        $unitNormalizer = function(Options $options, $value) use ($defaultUnit, $allowedUnits) {
            if(is_array($value)) {
                return $value;
            }
            if(preg_match('/([0-9\.]+)\s*(' . join('|', $allowedUnits) . ')/', $value, $matchedValue)) {
                $value = $matchedValue[1];
                $unit = $matchedValue[2];
            } else {
                $unit = $defaultUnit;
            }
            return array('value' => $value, 'unit' => $unit);
        };





        $resolver->setNormalizer('wrapper_attr', $wrapperAttrNormalizer);
        $resolver->setNormalizer('width', $unitNormalizer);
        $resolver->setNormalizer('height', $unitNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_merge(
            $view->vars,
            array(
                'wrapper_attr' => $options['wrapper_attr'],
                'width' => $options['width'],
                'height' => $options['height'],
                'font_size' => $options['font_size'],
                'mode' => $options['mode'],
                'theme' => $options['theme'],
                'tab_size' => $options['tab_size'],
                'read_only' => $options['read_only'],
                'use_soft_tabs' => $options['use_soft_tabs'],
                'use_wrap_mode' => $options['use_wrap_mode'],
                'show_print_margin' => $options['show_print_margin'],
                'highlight_active_line' => $options['highlight_active_line'],
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ace_editor';
    }
}

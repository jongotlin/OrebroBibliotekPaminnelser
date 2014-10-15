<?php

namespace JGI\AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'daysLeftColorCodeStart' => new \Twig_Function_Method($this, 'daysLeftColorCodeStart', ['is_safe' => ['html']]),
            'daysLeftColorCodeEnd' => new \Twig_Function_Method($this, 'daysLeftColorCodeEnd', ['is_safe' => ['html']]),
        );
    }

    /**
     * @param $daysLeft
     *
     * @return string
     */
    public function daysLeftColorCodeStart($daysLeft)
    {
        if ($daysLeft <= 0) {
            return '<span style="color: red">';
        } elseif ($daysLeft < 7) {
            return '<span style="color: orange">';
        }

        return '';
    }

    /**
     * @param $daysLeft
     *
     * @return string
     */
    public function daysLeftColorCodeEnd($daysLeft)
    {
        if ($daysLeft < 7) {
            return '</span>';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}

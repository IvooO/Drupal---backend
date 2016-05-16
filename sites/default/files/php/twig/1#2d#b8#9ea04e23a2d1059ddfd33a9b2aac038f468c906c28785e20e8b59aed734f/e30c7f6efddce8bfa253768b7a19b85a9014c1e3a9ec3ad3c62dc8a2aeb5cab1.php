<?php

/* {# inline_template_start #}route:view.content_by_category.page_1;arg_0={{tid}} */
class __TwigTemplate_2db89ea04e23a2d1059ddfd33a9b2aac038f468c906c28785e20e8b59aed734f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "route:view.content_by_category.page_1;arg_0=";
        echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, (isset($context["tid"]) ? $context["tid"] : null), "html", null, true);
    }

    public function getTemplateName()
    {
        return "{# inline_template_start #}route:view.content_by_category.page_1;arg_0={{tid}}";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}

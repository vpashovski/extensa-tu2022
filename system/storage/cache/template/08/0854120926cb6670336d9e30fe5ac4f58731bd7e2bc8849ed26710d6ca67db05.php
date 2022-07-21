<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* default/template/extension/module/tntsocialicon.twig */
class __TwigTemplate_31d836254acb7b8256b666a37be89f113e8077b0e72ec70ec950d7f0095f8dff extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["socialicons"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["socialicon"]) {
            // line 2
            echo "    ";
            echo twig_get_attribute($this->env, $this->source, $context["socialicon"], "tntsocialiconparent_class_name", [], "any", false, false, false, 2);
            echo "
    ";
            // line 3
            echo twig_get_attribute($this->env, $this->source, $context["socialicon"], "tntsocialiconparent_link", [], "any", false, false, false, 3);
            echo "
    ";
            // line 4
            echo twig_get_attribute($this->env, $this->source, $context["socialicon"], "tntsocialiconchild_title", [], "any", false, false, false, 4);
            echo "
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['socialicon'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "default/template/extension/module/tntsocialicon.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 4,  46 => 3,  41 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "default/template/extension/module/tntsocialicon.twig", "/home/exte7mok/public_html/catalog/view/theme/default/template/extension/module/tntsocialicon.twig");
    }
}

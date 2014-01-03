<?php

namespace Sculpin\Bundle\PostsBundle;

use Sculpin\Core\Source\Map\MapInterface;
use Sculpin\Core\Source\SourceInterface;

// @todo, Replace direct usage with injection.
use Sculpin\Core\Source\Filter\AntPathFilter;

class PostsContentTypeOverridesMap implements MapInterface
{
    // @todo, Does this property have to be private? This class is a modified
    // copy of DefaultDataMap which uses a private property. Protected seems
    // like it would be sufficient.
    private $content_type_overrides;

    private $path_filter_reflection_class;

    private $path_matcher_class;

    public function __construct(array $content_type_overrides = array(), string $path_filter_class_name = NULL, string $path_matcher_class_name = NULL)
    {
        $this->content_type_overrides = $content_type_overrides;

        // @todo This property should not be hard-coded.
        $this->overrideable_parameters = array(
          'permalink',
          'layout',
        );

        // Multiple real instances of this class will be instaniated in
        // assignType().
        $this->path_filter_reflection_class = new \ReflectionClass($path_filter_class_name ?: 'Sculpin\Core\Source\Filter\AntPathFilter');

        // The matcher only needs to be instantiated once. Therefore it gets
        // instaniated here and passed into the filter repeatedly in
        // assignType().
        $path_matcher_reflection_class = new \ReflectionClass($path_matcher_class_name ?: 'dflydev\util\antPathMatcher\AntPathMatcher');
        $this->path_matcher_class = $path_matcher_reflection_class->newInstance();
    }

    // @todo move out to a separate posts_map service.
    protected function assignType(SourceInterface $source)
    {
      foreach ($this->content_type_overrides as $type => $overrides) {
        if (!empty($overrides['paths'])) {
          $path_filter_arguments = array(
            $overrides['paths'],
            $this->path_matcher_class,
          );

          $content_type_overrides_path_filter = $this->path_filter_reflection_class->newInstanceArgs($path_filter_arguments);
          if ($content_type_overrides_path_filter->match($source)) {
            // If there isn't a type yet, set it.
            if (!$source->data()->get('type')) {
              $source->data()->set('type', $type);
            }
          }
        }
      }
    }

    public function process(SourceInterface $source)
    {
      $this->assignType($source);


      if ($type = $source->data()->get('type')) {
        if (!empty($this->content_type_overrides[$type])) {
          foreach ($this->content_type_overrides[$type] as $name => $value) {
            if (in_array($name, $this->overrideable_parameters))  {
              if (!$source->data()->get($name) && $value) {
                $source->data()->set($name, $value);
              }
            } 
          }
        }
      }
    }
}

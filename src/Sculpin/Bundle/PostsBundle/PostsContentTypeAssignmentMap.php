<?php

namespace Sculpin\Bundle\PostsBundle;

use Sculpin\Core\Source\Map\MapInterface;
use Sculpin\Core\Source\SourceInterface;

class PostsContentTypeAssignmentMap implements MapInterface
{
    // @todo, Does these properties have to be private? This class is a modified
    // copy of DefaultDataMap which uses a private property. Protected seems
    // like it would be sufficient.
    private $path_filter_reflection_class;
    private $path_matcher_class;

    public function __construct(array $content_type_overrides = array(), $path_filter_class_name = NULL, $path_matcher_class_name = NULL)
    {
        // Multiple real instances of this class will be instaniated in
        // assignType().
        $this->path_filter_reflection_class = new \ReflectionClass($path_filter_class_name ?: 'Sculpin\Core\Source\Filter\AntPathFilter');

        // The matcher only needs to be instantiated once. Therefore it gets
        // instaniated here and passed into the filter repeatedly in
        // assignType().
        $path_matcher_reflection_class = new \ReflectionClass($path_matcher_class_name ?: 'dflydev\util\antPathMatcher\AntPathMatcher');
        $this->path_matcher_class = $path_matcher_reflection_class->newInstance();
    }

    public function process(SourceInterface $source)
    {
      foreach ($this->content_type_overrides as $type => $overrides) {
        if (!empty($overrides['paths'])) {
          $path_filter_arguments = array(
            $overrides['paths'],
            $this->path_matcher_class,
          );

          // @todo I can't find examples of symfony code that calls
          // newInstanceArgs() outside of the Dependency Injection container.
          // That tells me this code should probably be refactored.
          $content_type_overrides_path_filter = $this->path_filter_reflection_class->newInstanceArgs($path_filter_arguments);
          if ($content_type_overrides_path_filter->match($source)) {
            // If there isn't a type yet, set it.
            if (!$source->data()->get('type')) {
              $source->data()->set('type', $type);
            }
          }
        }
      }
      // @todo, set a default post type.
      // $source->data()->set('type', $default_type);
    }
}

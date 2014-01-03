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

    public function __construct(array $content_type_overrides = array())
    {
        $this->content_type_overrides = $content_type_overrides;

        // @todo This property should not be hard-coded.
        $this->overrideable_parameters = array(
          'permalink',
          'layout',
        );
    }

    public function process(SourceInterface $source)
    {

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

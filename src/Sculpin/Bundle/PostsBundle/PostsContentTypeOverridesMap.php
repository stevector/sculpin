<?php

namespace Sculpin\Bundle\PostsBundle;

use Sculpin\Core\Source\Map\MapInterface;
use Sculpin\Core\Source\SourceInterface;

class PostsContentTypeOverridesMap implements MapInterface
{
    // @todo, Does this property have to be private? This class is a modified
    // copy of DefaultDataMap which uses a private property. Protected seems
    // like it would be sufficient.
    private $content_type_overrides;

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
      // @todo, The number of indentations here tells me that something should
      // be refactored.
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

<?php
namespace Starbug\Core\Routing;

class Route {

  use Traits\RouteProperties;
  use Traits\Routes;
  use Traits\Resolvers;

  public function __construct($path, $controller = null, $options = [], $parent = null) {
    $this->path = $path;
    $this->controller = $controller;
    $this->options = $options;
    $this->parent = $parent;
  }
}

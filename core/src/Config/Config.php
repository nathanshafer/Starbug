<?php
/**
 * An implementation of the ConfigInterface which reads json files from the filesystem
 */
namespace Starbug\Core;
class Config implements ConfigInterface {

  private $locator;
  private $configs;
  private $providers;

  public function __construct(ResourceLocatorInterface $locator) {
    $this->locator = $locator;
    $this->configs = [];
    $this->providers = [];
  }

  /**
  * get a configuration value
  * @param string $name the name of the configuration entry, such as 'themes' or 'fixtures.base'
  * @param string $scope the scope/category of the configuration item
  * providing first.second.third will open up the file first.json and look for the key "second" and within that, a key "third"
  */
  public function get($key, $scope = "etc") {
    if (isset($this->providers[$scope])) return $this->providers[$scope]->get($key, $scope);

    $parts = explode(".", $key);

    $key = array_shift($parts);

    if (empty($this->configs[$key])) {
      $resources = $this->locator->locate($key.".json", $scope);
      $result = [];
      foreach ($resources as $resource) {
        $data = $this->decode(file_get_contents($resource));
        $result = array_merge_recursive($result, $data);
      }
      $this->configs[$key] = $result;
    }

    $value = $this->configs[$key];

    while (!empty($parts)) {
      $next = array_shift($parts);
      $value = $value[$next];
    }

    return $value;
  }

  public function provide($scope, ConfigInterface $provider) {
    $this->providers[$scope] = $provider;
  }

  private function decode($text) {
    $raw = explode("\n", $text);
    foreach ($raw as $idx => $item) {
      $first = substr(trim($item), 0, 1);
      if (!(in_array($first, ['"', '{', '}', '[', ']']) || is_numeric($first))) unset($raw[$idx]);
    }
    return json_decode(join("\n", $raw), true);
  }
}

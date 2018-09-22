<?php
namespace Starbug\Content;

use Starbug\Core\QueryHook;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\InputFilterInterface;

class StoreBlocksHook extends QueryHook {
  public function __construct(DatabaseInterface $db, InputFilterInterface $filter) {
    $this->db = $db;
    $this->filter = $filter;
  }
  public function validate($query, $key, $value, $column, $argument) {
    $query->exclude($key);
    if ($query->isInsert()) {
      $this->db->queue("blocks", ["type" => "text",  "region" => "content",  "position" => 1, "pages_id" => "", "content" => $this->filter->html($value['content-1'])]);
    } else {
      $blocks = $this->db->query("blocks")->select("blocks.*")->condition($query->model."_id", $query->getId())->all();
      foreach ($blocks as $block) {
        $key = $block['region'].'-'.$block['position'];
        if (isset($value[$key])) $this->db->queue("blocks", ["id" => $block['id'], "content" => $this->filter->html($value[$key])]);
      }
    }
    return $value;
  }
  public function before_delete($query, $column, $argument) {
    $id = $query->getId();
    $this->db->query("blocks")->condition($query->model."_id", $id)->delete();
  }
}

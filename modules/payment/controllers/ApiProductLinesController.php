<?php
namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiProductLinesController extends ApiController {
  public $model = "product_lines";
  public function __construct(SessionHandlerInterface $session, Cart $cart) {
    $this->session = $session;
    $this->cart = $cart;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function cart() {
    $params = [];
    $queryParams = $this->request->getQueryParams();
    if (empty($queryParams["order"])) {
      $params["order"] = $this->cart->get("id");
    }
    $this->api->render("ProductLines", $params);
  }
  public function order() {
    $this->api->render("ProductLines");
  }
  public function filterQuery($collection, $query, $ops) {
    $cid = $this->request->getCookieParams()["cid"];
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) {
      $query->condition(
        $query->createCondition()
          ->condition("product_lines.orders_id.token", $cid)
          ->orCondition("product_lines.orders_id.owner", $this->session->getUserId())
      );
    }
    return $query;
  }
}

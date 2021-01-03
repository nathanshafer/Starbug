<?php

namespace Starbug\Payment;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiShippingMethodsController extends ApiController {
  public $model = "shipping_methods";
  public function __construct(IdentityInterface $user, Cart $cart) {
    $this->user = $user;
    $this->cart = $cart;
  }
  public function admin() {
    $this->api->render("AdminShippingMethods");
  }
  public function select() {
    $params = [];
    $queryParams = $this->request->getQueryParams();
    if (empty($queryParams["order"])) {
      $params["order"] = $this->cart->get("id");
    }
    $this->api->render("SelectShippingMethods", $params);
  }
}

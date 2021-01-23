<?php
namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class CheckoutController extends Controller {
  public function __construct(Cart $cart, SessionHandlerInterface $session, DatabaseInterface $db) {
    $this->cart = $cart;
    $this->session = $session;
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "orders");
    $this->assign("cart", $this->cart);
  }
  public function defaultAction() {
    if ($this->db->success("orders", "checkout")) {
      $this->response->redirect("checkout/payment");
    } elseif (empty($this->cart)) {
      $this->render("cart/empty.html");
    } elseif ($this->session->loggedIn()) {
      $this->render("checkout/default.html");
    } else {
      $this->render("checkout/login.html");
    }
  }
  public function guest() {
    if ($this->db->success("orders", "checkout")) {
      $this->response->redirect("checkout/payment");
    } elseif ($this->session->loggedIn()) {
      $this->response->redirect("checkout");
    } elseif (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/default.html");
    }
  }
  public function payment() {
    if ($this->db->success("orders", "payment")) {
      $id = $this->request->getParsedBody()["orders"]["id"];
      $this->response->redirect("checkout/success/".$id);
    } elseif (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/payment.html");
    }
  }
  public function success($id) {
    $this->assign("id", $id);
    $this->render("checkout/success.html");
  }
}

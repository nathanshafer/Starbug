<?php
/**
 * Users model
 * @ingroup models
 */
class Users {

	/**
	 * A function for an administrator to create and update users
	 */
	function create($user) {
		if (logged_in("root") || logged_in("admin")) {
			foreach ($user as $k => $v) if (empty($v) && $k != "email") unset($user[$k]);
		}
		$this->store($user);
		if ((!errors()) && (empty($user['id']))) {
			$uid = $this->insert_id;
			$data = array("user" => get("users", $uid));
			$data['user']['password'] = $user['password'];
			$this->mailer->send_email(array("template" => "Account Creation", "to" => $user['email']), $data);
		}
	}

	function delete($user) {
		store("users", array("id" => $user['id'], "statuses" => "deleted"));
	}

	/**
	 * A function for new users to register themselves
	 */
	function register() {

	}

	/**
	 * A function for current users to update their profile
	 */
	function update_profile($user) {
		return $this->store($user);
	}

	/**
	 * A function for logging in
	 */
	function login($login) {
		$user = $this->query("select:users.*,users.groups as groups,users.statuses as statuses  where:email=?  limit:1", array($login['email']));
		if (Session::authenticate($user['password'], $login['password'], $user['id'], Etc::HMAC_KEY)) {
			$user['groups'] = explode(",", $user['groups']);
			$user['statuses'] = explode(",", $user['statuses']);
			sb()->user = $user;
			unset($user['password']);
			$this->store(array("id" => $user['id'], "last_visit" => date("Y-m-d H:i:s")));
			if (logged_in('admin') || logged_in('root')) redirect(uri('admin'));
		} else {
			error("That email and password combination was not found.", "email");
		}
		unset($_POST['users']['password']);
	}

	/**
	 * for logging out
	 */
	function logout() {
		Session::destroy();
		return array();
	}

	/**
	 * resets a users password and emails it to them
	 */
	function reset_password($fields) {
		$email_address = trim($fields['email']);
		if (empty($email_address)) error("Please enter your email address.", "email");
		else {
			$user = $this->query("where:email='".$email_address."'  limit:1");
			if (!empty($user)) {
				$id = $user['id'];
				if (empty($id)) error("Sorry, the email address you entered was not found. Please retry.", "email");
				else {
					$new_password = mt_rand(1000000, 9999999);
					$this->store("id:$id  password:$new_password");
					$result = exec("sb email password_reset $id $new_password");
					if ((int)$result != 1) error("Sorry, there was a problem emailing to your address. Please retry.", "email");
				}
			} else error("Sorry, the email address you entered was not found. Please retry.", "email");
		}
	}

	function query_admin($query, &$ops) {
		$query->select("users.*");
		$query->select("users.groups.id as groups");
		$query->select("users.statuses.id as statuses");
		if (!empty($ops['group']) && is_numeric($ops['group'])) {
			$query->condition("users.groups.id", $ops['group']);
		}

		if (!empty($ops['status']) && is_numeric($ops['status'])) $query->condition("users.statuses.id", $ops['status']);
		else $query->condition("users.statuses.slug", "deleted", "!=", array("ornull" => true));
		return $query;
	}

	function display_admin($display, &$options) {
		$display->add("first_name", "last_name", "email", "last_visit", "groups", "statuses  label:Status");
	}

	function display_form($display, &$options) {
		//parent::display_form($display, $options);
		$display->layout->add("top  left:div.col-md-6  right:div.col-md-6");
		$display->layout->put('left', 'h2', 'User Information');
		$display->layout->put('right', 'h2', 'Login Credentials');
		$display->add("first_name  pane:left");
		$display->add("last_name  pane:left");
		$display->add("email  pane:right");
		$display->add("password  pane:right");
		$display->add("password_confirm  input_type:password  pane:right");
		$display->add("groups  input_type:multiple_category_select  taxonomy:groups  pane:right");
	}

	function display_login($display, $ops) {
		$display->add("email", "password");
		$display->submit_label = "Login";
	}

	function display_reset_password($display, $ops) {
		$display->add("email");
		$display->submit_label = "Reset Password";
	}

	function filter($row) {
		//even though it shouldn't be useful to attackers,
		//we don't want the password hash to be returned in api calls
		unset($row['password']);
		return $row;
	}

	function display_search($display, $ops) {
		parent::display_search($display, $ops);
		$display->add("group  input_type:category_select  taxonomy:groups  optional:Any Group  nolabel:");
		$display->add("status  input_type:category_select  taxonomy:statuses  optional:Any Status  nolabel:");
	}
}
?>

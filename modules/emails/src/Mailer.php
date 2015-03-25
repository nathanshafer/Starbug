<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/emails/src/Mailer.php
 * @author Ali Gangji <ali@neonrain.com>
 */

class Mailer extends PHPMailer implements MailerInterface {

	private $host;
	private $username;
	private $password;
	private $from_email;
	private $from_name;

	function __construct(ConfigInterface $config) {
		$this->host = $config->get("email_host", "settings");
		$this->username = $config->get("email_username", "settings");
		$this->password = $config->get("email_password", "settings");
		$this->from_email = $config->get("email_address", "settings");
		$this->from_name = $config->get("site_name", "settings");
		$port = $config->get("email_port", "settings");
		$secure = $config->get("email_secure", "settings");
		if ($this->host) {
			$this->IsSMTP(); // send via SMTP
			$this->Host     = $this->host;
			$this->SMTPAuth = true;  // turn on SMTP authentication
			$this->Username = $this->username;    // SMTP username
			$this->Password = $this->password;    // SMTP password
		}
		if ($this->from_email) $this->From = $this->from_email;
		if ($this->from_name) $this->FromName = $this->from_name;
		if (!empty($port)) $this->Port = $port;
		if (!empty($secure)) $this->SMTPSecure = $secure;
		$this->WordWrap = 50;
		$this->IsHTML(true);
	}

	/**
	 * send an email email
	 * @param array $options
	 * @param array $data
	 */
	function send($options = array(), $data = array()) {
		$options = $options;
		$data = $data;
		$data['url_flags'] = 'u';

		//get template params
		if (!empty($options['template'])) {
			$template = query("email_templates")->condition(array(
				"name" => $options['template'],
				"email_templates.statuses" => "published"
			))->one();
			if (!empty($template)) $options = array_merge($template, $options);
		}

		//set mailer params
		if (!empty($options['from'])) $this->From = token_replace($options['from'], $data);
		if (!empty($options['from_name'])) $this->FromName = token_replace($options['from_name'], $data);
		if (!empty($options['subject'])) $this->Subject = token_replace($options['subject'], $data);
		if (!empty($options['body'])) $this->Body = token_replace($options['body'], $data);
		if (!empty($options['to'])) {
			$to = $options['to'];
			if (!is_array($to)) $to = explode(",", $to);
			foreach ($to as $email) $this->AddAddress(token_replace(trim($email), $data));
		}
		if (!empty($options['cc'])) {
			if (!is_array($options['cc'])) $options['cc'] = explode(',', $options['cc']);
			foreach ($options['cc'] as $cc) $this->AddCC(token_replace($cc, $data));
		}
		if (!empty($options['bcc'])) {
			if (!is_array($options['bcc'])) $options['bcc'] = explode(',', $options['bcc']);
			foreach ($options['bcc'] as $bcc) $this->AddBCC(token_replace($bcc, $data));
		}
		if (!empty($options['attachments'])) {
			$attachments = $options['attachments'];
			foreach ($attachment as $a) {
				if (is_array($a)) $this->AddAttachment($a[0], $a[1]);
				else $this->AddAttachment($a);
			}
		}

		//send mail
		$result = $this->Send();
		return $result;
	}

	/**
	 * get errors
	 */
	function errors() {
		return $this->ErrorInfo;
	}
}

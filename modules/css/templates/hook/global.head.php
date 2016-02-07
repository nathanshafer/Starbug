	<?php if (Etc::ENVIRONMENT == "production") { ?>
			<link rel="stylesheet" href="<?php echo $this->url->build("var/public/stylesheets/".$response->theme."-screen.css"); ?>" type="text/css" media="screen, projection">
	<?php } else { ?>

		<?php
			$styles = $this->config->get("info.styles", 'themes/'.$response->theme);
			if (empty($styles['screen'])) $styles['screen'] = array();
			if (empty($styles['print'])) $styles['print'] = array();
			if (empty($styles['ie'])) $styles['ie'] = array();
			if (empty($styles['less'])) $styles['less'] = false;
		?>

		<?php if ($styles['less'] && file_exists(BASE_DIR."/app/themes/".$response->theme."/public/stylesheets/custom-screen.less")) { ?>
			<link rel="stylesheet/less" href="<?php echo $this->url->build("app/themes/".$response->theme."/public/stylesheets/custom-screen.less"); ?>" type="text/css" media="screen, projection">
		<?php } else if (file_exists(BASE_DIR."/app/themes/".$response->theme."/public/stylesheets/custom-screen.css")) { ?>
			<link rel="stylesheet" href="<?php echo $this->url->build("app/themes/".$response->theme."/public/stylesheets/custom-screen.css"); ?>" type="text/css" media="screen, projection">
		<?php } ?>

		<?php if (file_exists(BASE_DIR."/app/themes/".$response->theme."/public/stylesheets/custom-print.css")) { ?>
			<link rel="stylesheet" href="<?php echo $this->url->build("app/themes/".$response->theme."/public/stylesheets/custom-print.css"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php foreach ($styles['screen'] as $screen) { ?>
			<link rel="stylesheet" href="<?php echo $this->url->build("app/themes/".$response->theme."/public/stylesheets/$screen"); ?>" type="text/css" media="screen, projection">
		<?php } ?>
		<?php foreach ($styles['print'] as $print) { ?>
			<link rel="stylesheet" href="<?php echo $this->url->build("app/themes/".$response->theme."/public/styesheets/$print"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php if ($styles['less']) { ?>
			<script type="text/javascript">
				less = { env: 'development' };
			</script>
			<script src="<?php echo $this->url->build("libraries/less/dist/less.min.js"); ?>" type="text/javascript"></script>
		<?php } ?>
	<?php } ?>

<?php
$options = schema($model);
?>
<div class="pull-right">
		<?php
			link_to("Export CSV", "", array("href" => "javascript:window.location.href = ".$model."_grid.store.last_query.replace('json', 'csv');", "class" => "btn btn-default"));
			link_to("New Taxonomy <b class=\"fa fa-plus\"></b>", $request->path."/new", "class:btn btn-default");
		?>
</div>
<?php render_form(array($model."/search", "search")); ?>
<br/>

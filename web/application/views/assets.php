
		<meta charset="UTF-8" />
		<script type="text/javascript" src="<?php echo ASSETS_URL; ?>js/libs/modernizr.js"></script>
		<script type="text/javascript" src="<?php echo ASSETS_URL; ?>js/libs/underscore-min.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>

		<script type="text/javascript" id="js_setup">
			if(window.google && window.google.load) {
				google.load('jquery', '1');
				google.load('jqueryui', '1');

				// var css_jqueryui = document.createElement('link');
				// css_jqueryui.type = "text/css";
				// css_jqueryui.media = "all";
				// css_jqueryui.href = "http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css";

				// var head = document.getElementsByTagName('head')[0];
				// var first = head.firstChild;

				// head.insertBefore( css_jqueryui, first );
			} else {
				var js_jquery = document.createElement('script');
				js_jquery.type = "text/javascript";
				js_jquery.src = "<?php echo ASSETS_URL; ?>fallback/js/jquery-1.7.1.min.js";

				var js_jqueryui = document.createElement('script');
				js_jqueryui.type = "text/javascript";
				js_jqueryui.src = "<?php echo ASSETS_URL; ?>fallback/js/jquery-ui-1.8.17.custom.min.js";

				// var css_jqueryui = document.createElement('link');
				// css_jqueryui.type = "text/css";
				// css_jqueryui.media = "all";
				// css_jqueryui.href = "<?php echo ASSETS_URL; ?>fallback/css/smoothness/jquery-ui-1.8.17.custom.css";

				var head = document.getElementsByTagName('head')[0];
				var first = head.firstChild;

				head.insertBefore( js_jquery, first );
				head.insertBefore( js_jqueryui, first );
				// head.insertBefore( css_jqueryui, first );
			}

			var base_url = '<?php echo BASE_URL; ?>';
			var assets_url = '<?php echo ASSETS_URL; ?>';
			var fbAppID = '<?php echo FB_APP_ID; ?>';
		</script>

		<script type="text/javascript" src="<?php echo ASSETS_URL; ?>js/libs/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="<?php echo ASSETS_URL; ?>bootstrap/js/bootstrap.min.js"></script>

		<link href='http://fonts.googleapis.com/css?family=Magra:400,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo ASSETS_URL; ?>fallback/css/smoothness/jquery-ui-1.8.17.custom.css" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo ASSETS_URL; ?>bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo ASSETS_URL; ?>bootstrap/css/bootstrap-responsive.min.css" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo ASSETS_URL; ?>css/style.all.css" />

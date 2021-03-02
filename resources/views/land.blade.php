<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<title></title>

		<script type="text/javascript">
			window.addEventListener('beforeunload', function(e) {
				window.opener.checkBc("{{ $result }}");
			});

			window.close();
		</script>

		<style>
			.col-8 {
				-webkit-box-flex: 0;
			    flex: 0 0 66.6666666667%;
			    max-width: 66.6666666667%;
			}
			.mx-auto {
				margin-left: auto;
				margin-right: auto;
			}
			.text-warning {
				color: orange;
			}
		</style>
	</head>

	<body>
		<div class="col-8 mx-auto">
			<h4 class="text-warning">Close this window to finish your payment.</h4>
			<p>{{ $result }}</p>
		</div>
	</body>
</html>
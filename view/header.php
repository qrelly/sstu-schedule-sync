<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title><?=$title ?></title>
		<style type="text/css">
			body {
				font: 1em "Helvetica Neue", Helvetica, Arial, sans-serif;
				cursor: default;
				color: #333;
				padding: 0;
			}

			.hidden {
				display: none;
			}

			#wrapper {
				width: 60%;
				margin: 0 auto;
			}

				#wrapper h1 {
					text-align: center;
					margin: 3rem 0;
					color: #222;
				}

				#wrapper p {
					text-indent: 1rem;
				}

				.action {
					-webkit-transition: all 0.1s ease;
					-moz-transition: all 0.1s ease;
					transition: all 0.1s ease;
					background-color: #dd4b39;
					text-decoration: none;
					border-radius: 0.6rem;
					text-align: center;
					font-weight: bold;
					margin: 3rem auto;
					cursor: default;
					padding: 1rem;
					display: block;
					color: white;
					width: 264px;
					border: none;
				}

				.action:hover {
					background-color: #e74b37;
					-webkit-box-shadow: inset 0 -0.2rem 0 rgba(0, 0, 0, .20);
					-moz-box-shadow: inset 0 -0.2rem 0 rgba(0, 0, 0, .20);
					box-shadow: inset 0 -0.2rem 0 rgba(0, 0, 0, .20);
				}

				.action:active {
					background-color: #be3e2e;
					-webkit-box-shadow: inset 0 0.2rem 0 rgba(0, 0, 0, .20);
					-moz-box-shadow: inset 0 0.2rem 0 rgba(0, 0, 0, .20);
					box-shadow: inset 0 0.2rem 0 rgba(0, 0, 0, .20);
				}

				input, select {
					padding: 0.4rem;
					font: inherit;
					margin: 0;
				}

				.info {
					font-style: italic;
					font-size: 0.8rem;
					display: block;
					color: #666;
				}

					.info strong {
						color: black;
					}

				#footer {
					text-align: center;
					font-size: 0.8rem;
					color: #999;
				}

					#footer a {
						text-decoration: none;
						color: #dd4b39;
					}

					#footer a:hover {
						text-decoration: underline;
						color: #be3e2e;
					}
		</style>
		<script type="text/javascript">
			function update_tip_info ( )
			{
				var	student_info = document.getElementById('tip-for-students'),
					teacher_info = document.getElementById('tip-for-teachers'),
					source_type = document.getElementById('source-type'),
					input_field = document.getElementById('element_id');

				switch (source_type.options[source_type.selectedIndex].value)
				{
					default:
					case 'group_id':
					{
						student_info.className = '';
						teacher_info.className = 'hidden';
						input_field.placeholder = 'Номер группы';

						break;
					}
					case 'teacher_id':
					{
						student_info.className = 'hidden';
						teacher_info.className = '';
						input_field.placeholder = 'Номер преподавателя';

						break;
					}
				}
			}
		</script>
	</head>
<body>
	<div id="wrapper">
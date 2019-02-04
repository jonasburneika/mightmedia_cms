<?php

if ( !defined( "OK" ) || !ar_admin( basename( __file__ ) ) ) {
	redirect( 'location: http://' . $_SERVER["HTTP_HOST"] );
}

if ( !isset( $_GET['v'] ) ) {
	$_GET['v'] = 1;
	$url['v']  = 1;
}

//Puslapiavimui
if ( isset( $url['p'] ) && isnum( $url['p'] ) && $url['p'] > 0 ) {
	$p = (int)$url['p'];
} else {
	$p = 0;
}
$limit = 15;

if(BUTTONS_BLOCK) {
	lentele(getLangText('admin', 'poll'), buttonsMenu(buttons('polls')));
}

//delete poll
if (isset($url['t'])) {
	$delId = (int)$url['t'];
	$delQuestionsQuery = "DELETE FROM  `" . LENTELES_PRIESAGA . "poll_questions` WHERE `id`=" . escape($delId);
	if(mysql_query1($delQuestionsQuery)) {
		$delAnswersQuery = "DELETE FROM  `" . LENTELES_PRIESAGA . "poll_answers` WHERE `question_id`=" . escape($delId);	
		if(mysql_query1($delAnswersQuery)) {
			$delVotesQuery = "DELETE FROM  `" . LENTELES_PRIESAGA . "poll_votes` WHERE `question_id`=" . escape($delId);
			mysql_query1($delVotesQuery);
		}

		redirect(
			url("?id," . $url['id'] . ";a," . $url['a']. ";v,2"),
			"header",
			[
				'type'		=> 'success',
				'message' 	=> getLangText('admin', 'post_deleted')
			]
		);
	} else {
		notifyMsg(
			[
				'type'		=> 'error',
				'message' 	=> input(mysqli_error($prisijungimas_prie_mysql))
			]
		);
	}
}

//poll creation
if ($url['v'] == 1) {
	if (isset($_POST['question'])) {
		$insertQuery = "INSERT INTO `" . LENTELES_PRIESAGA . "poll_questions` (`question`, `radio`, `shown`, `only_guests`, `author_id`, `author_name`, `lang`) VALUES (" . escape($_POST['question']) . ", " . escape((int)$_POST['type']) . ", " . escape((int)$_POST['shown']) . ", " . escape((int)$_POST['only_guests']) . ", " . escape(getSession('id')) . "," . escape(getSession('username')) . ", " . escape(lang()) . ")";
		if(mysql_query1($insertQuery)) {
			$selectQuestionsQuery = "SELECT `id` FROM `" . LENTELES_PRIESAGA . "poll_questions` WHERE `lang` = " . escape(lang()) . " ORDER BY `id` DESC LIMIT 1";
			
			if($qid = mysql_query1($selectQuestionsQuery, 3600)) {
				foreach ($_POST['answers'] as $ans) {
					mysql_query1( "INSERT INTO `" . LENTELES_PRIESAGA . "poll_answers` (`question_id`, `answer`, `lang`) VALUES (" . escape( $qid['id'] ) . ", " . escape( $ans ) . "," . escape( lang() ) . ")" );
				}
			}
			
			redirect(
				url("?id," . $url['id'] . ";a," . $url['a']),
				"header",
				[
					'type'		=> 'success',
					'message' 	=> getLangText('admin', 'poll_created')
				]
			);
		} else {
			notifyMsg(
				[
					'type'		=> 'error',
					'message' 	=> input(mysqli_error($prisijungimas_prie_mysql))
				]
			);
		}
		
	}

	$pollForm = [
		"Form"							=> [
			"action" 	=> "", 
			"method" 	=> "post",
			"name" 		=> "reg"
		],
	    getLangText('admin', 'poll_question') => [
			"type" => "text", 
			"name" => "question"
		],
	    getLangText('admin', 'poll_votecan')  => [
			"type" 	=> "select", 
			"name" 	=> "only_guests", 
			"value" => [
				0 => getLangText('admin', 'poll_all'), 
				1 => getLangText('admin', 'poll_membs')
			]
		],

	    getLangText('admin', 'poll_type')     => [
			"type" => "select", 
			"name" => "type", 
			"value" => [
				0 => 'checkbox', 
				1 => 'radio'
			]
		],
		
		getLangText('admin', 'poll_answers')  => [
			"type" 	=> "string", 
			"value" => "<a href=\"#\" onclick=\"return false;\" id=\"add\">
			<img src=\"" . ROOT . "core/assets/images/icons/plus.png\" alt=\"[+]\" /></a> 
			<a href=\"#\" onclick=\"return false;\" id=\"remove\">
			<img src=\"" . ROOT . "core/assets/images/icons/minus.png\" alt=\"[-]\" /></a>
			<div id=\"inputs\">
			<div class='form-line'>
			<input type=\"text\" name=\"answers[]\" class=\"form-control\" placeholder='" . getLangText('admin', 'poll_question') . "' />
			</div>
			</div>"
		],

		getLangText('admin', 'poll_active')	=> [
			'type'		=> 'switch',
			'value'		=> 1,
			'name'		=> 'shown',
			'id'		=> 'shown',
			'form_line'	=> 'form-not-line',
			'checked' 	=> true,
		],

		''								=> [
			"type" 		=> "submit", 
			'form_line'	=> 'form-not-line',
			"value" 	=> getLangText('admin', 'poll_create')
		]
	];

	$formClass = new Form($pollForm);
	lentele(getLangText('admin', 'poll_create'), $formClass->render());
	?>
  	<script type="text/javascript">
		var i = $('input').size() + 1;
		$('a#add').click(function() {
			$($('#inputs .form-line:last')[0].outerHTML).animate(
				{ opacity: "show" }, 
				"fast", 
				function(){
					$('#inputs input:last').focus();
				}
			).appendTo('#inputs'); 

			i++;
		});
		$('a#remove').click(function() {
			if($('#inputs .form-line').length > 1 && i > 1) {
				$('#inputs .form-line:last').animate({opacity:"hide"}, "slow").remove();
				i--;
			}
		});
	</script>
	<?php

} elseif ($url['v'] == 2) {
	if (isset($url['e'])) {
		if (isset($_POST['update'])) {
			$updateQuery = "UPDATE `" . LENTELES_PRIESAGA . "poll_questions` SET 
			`question`=" . escape($_POST['question']) . ", 
			`radio`=" . escape((int)$_POST['type']) . ", 
			`shown`=" . escape((int)$_POST['shown']) . ", 
			`only_guests`=" . escape((int)$_POST['only_guests']) . " 
			WHERE `id`=" . escape($url['e']);
;
			if (mysql_query1($updateQuery)) {
				redirect(
					url("?id," . $url['id'] . ";a," . $url['a']),
					"header",
					[
						'type'		=> 'success',
						'message' 	=> getLangText('admin', 'post_updated')
					]
				);
		
			} else {
				notifyMsg(
					[
						'type'		=> 'error',
						'message' 	=> input(mysqli_error($prisijungimas_prie_mysql))
					]
				);
			}
		}

		$quest  = mysql_query1( "SELECT * FROM  `" . LENTELES_PRIESAGA . "poll_questions` WHERE `id`=" . escape( $_GET['e'] ) . " LIMIT 1", 3600 );
		$inputs = [ 
			"Form"							=> [
				"action" 	=> "", 
				"method" 	=> "post", 
				"name" 		=> "reg"
			],

			getLangText('admin', 'poll_question') => [
				"type" 	=> "text", 
				"name" 	=> "question", 
				"value" => input($quest['question'])
			],
			
			getLangText('admin', 'poll_votecan')  => [
				"type" 		=> "select", 
				"selected" 	=> input($quest['only_guests']), 
				"name" 		=> "only_guests", 
				"value" 	=> [
					0 => getLangText('admin', 'poll_all'), 
					1 => getLangText('admin', 'poll_membs')
				]
			],

			getLangText('admin', 'poll_type')     => [
				"type" => "select", 
				"name" => "type", 
				"value" => [
					0 => 'checkbox', 
					1 => 'radio'
				], 
				"selected" => input($quest['radio'])
			],
			
			getLangText('admin', 'poll_active')	=> [
				'type'		=> 'switch',
				'value'		=> 1,
				'name'		=> 'shown',
				'id'		=> 'shown',
				'form_line'	=> 'form-not-line',
				'checked' 	=> (! empty($quest['shown']) && $quest['shown'] == 1 ? true : false),
			],

			""								=> [
				"type" 		=> "submit",
				"name" 		=> "update", 
				'form_line'	=> 'form-not-line',
				"value" 	=> getLangText('admin', 'edit')
			]
		];

		$formClass = new Form($inputs);
		lentele(getLangText('admin', 'poll_edit'), $formClass->render());
	}

	$viso  = kiek( "poll_questions", "WHERE `lang` = " . escape( lang() ) . "" );
	$quest = mysql_query1( "SELECT * FROM `" . LENTELES_PRIESAGA . "poll_questions` WHERE `lang` = " . escape( lang() ) . " ORDER BY `id` DESC LIMIT {$p},{$limit}", 3600 );
	foreach ( $quest as $row ) {
		$info[] = [
			getLangText('admin', 'poll_active_q') => ( $row['shown'] == 1 ? '<img src="' . ROOT . '/core/assets/images/icons/status_online.png" alt="" />' : '<img src="' . ROOT . '/core/assets/images/icons/status_offline.png" alt="" />' ),
			getLangText('admin', 'poll')          => input( $row['question'] ),
			getLangText('system', 'edit')         => " <a href='" . url( "?id,{$url['id']};a,{$url['a']};v,{$url['v']};e," . $row['id'] ) . "' data-toggle='tooltip' title='" . getLangText('admin',  'edit') . "'><img src='" . ROOT . "core/assets/images/icons/pencil.png'></a> 
			<a href='" . url( "?id,{$url['id']};a,{$url['a']};t," . $row['id'] ) . "' data-toggle='tooltip' title='" . getLangText('admin',  'delete') . "' onclick=\"return confirm('" . getLangText('system', 'delete_confirm') . "')\"><img src='" . ROOT . "core/assets/images/icons/cross.png'></a>" 
		];
	}
	if (! empty($info)) {

		$tableClass   = new Table($info);
		lentele(getLangText('admin', 'poll_edit'), $tableClass->render());
		// if list is bigger than limit, then we show list with pagination
		if ( $viso > $limit ) {
			lentele( getLangText('system', 'pages'), pages( $p, $limit, $viso, 10 ) );
		}

	} else {
		notifyMsg(
			[
				'type'		=> 'error',
				'message' 	=> getLangText('admin', 'poll_no')
			]
		);
	}
}
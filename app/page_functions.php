<?php

function handleIndexPage()
{
	$user = Request::getUser();

	$user_sessions = $user->getSessions();

	$sessions_array = array();
	foreach ($user_sessions as $session)
	{
		$cur_session = array();
		$cur_session['id'] = $session->getID();
		$cur_session['created_at'] = $session->getCreatedAt();
		$cur_session['expires_at'] = $session->getExpiresAt();
		$cur_session['description'] = $session->getDescription();
		$cur_session['tr_css_classs'] = ($session->isExpired()) ? 'danger' : 'success';
		$cur_session['feedback_link'] = $session->getFeedbackLink();
		$sessions_array[] = $cur_session;
	}

	$tpl = Template::create('pages/index.tpl');
	$tpl->assign('sessions', $sessions_array);
	$tpl->display();
}

function handleLoginPage()
{
	if (User::isLoggedIn())
	{
		Request::redirect('/');
		exit;
	}
	$tpl = Template::create('pages/login.tpl');
	$tpl->display();
}

function handleLogout()
{
	Request::redirect('/api/auth.php?logout=1');
	exit;
}

// Feedback pages
function handleFeedbackPage()
{
	echo 'Please enter a valid session ID.';
	exit;
}
function handleFeedbackSessionPage($session_id)
{
	// Decode session ID
	$session_id = base64_decode($session_id);
	$session_id = explode('__', $session_id)[0];

	$error_message = Request::getSessionVariable('feedback_error_message');
	Request::deleteSessionVariable('feedback_error_message');

	$feedback_temp_message = Request::getSessionVariable('feedback_temp_message');
	Request::deleteSessionVariable('feedback_temp_message');

	$tpl = Template::create('pages/feedback.tpl');
	$tpl->assign('session_id', $session_id);
	$tpl->assign('error_message', $error_message);
	$tpl->assign('feedback_temp_message', $feedback_temp_message);
	$tpl->display();
}
function handleFeedbackConfirmationPage()
{
	$tpl = Template::create('pages/feedback-confirmation.tpl');
	$tpl->display();
}


/** 404 Page **/
function handle404Page()
{
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	$tpl = Template::create('404.tpl');
	$tpl->display();
}

/** Handle Routing **/
function handleRouting(AltoRouter $router)
{
	$match = $router->match();
	if ($match && is_callable($match['target']))
	{
		call_user_func_array($match['target'], $match['params']);
	}
	else
	{
		handle404Page();
	}
}
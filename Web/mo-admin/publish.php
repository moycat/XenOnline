<?php
function publish_jump($status)
{
	header("Location: problem.php?result=$status");
	exit(0);
}

$no_display = True;
require_once 'header.php';
if (!isset($_POST['action']))
{
	publish_jump(0);
}
switch ($_POST['action'])
{
	case 'add':
		if (add_problem())
		{
			publish_jump(1);
		}
		else
		{
			header("Location: problem_edit.php?action=add");
		}
	case 'edit':
		if (edit_problem())
		{
			publish_jump(2);
		}
		else
		{
			publish_jump(-2);
		}
	default:
		publish_jump(0);
}

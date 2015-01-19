<?php

/*
 *	Includes
 */
require_once './schedule.php';
require_once './src/Google_Client.php';
require_once './src/contrib/Google_CalendarService.php';

/*
 *	Session
 */
session_start();

/*
 *	App setup
 */
ini_set('max_execution_time', 300); // 5 min.

$title = 'Синхронизация расписания СГТУ c календарём Google';

$client = new Google_Client();
$client->setApplicationName($title);
$client->setClientId('');
$client->setClientSecret('');
$client->setRedirectUri('');
$client->setDeveloperKey('');

$service = new Google_CalendarService($client);

// Logout action
if (isset($_GET['logout']))
{
	unset($_SESSION['token']);

	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

	exit;
}

// Get code action
if (isset($_GET['code']))
{
	$client->authenticate($_GET['code']);
	
	$_SESSION['token'] = $client->getAccessToken();

	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

	exit;
}

require_once './view/header.php';

// Set token action
if (isset($_SESSION['token']))
{
	$client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken())
{
	if (empty($_POST['element_id']))
	{
		require_once './view/enter.php';
	}
	else
	{
		//$element_id = (int) $_POST['element_id'];
		$element_id = $_POST['element_id'];
		$source_type = trim($_POST['source_type']);
		$reminder_time = (int) $_POST['reminder_time'];
		$separate_calendar = isset($_POST['separate_calendar']) ? true : false;
		$force_cache_update = isset($_POST['force_cache_update']) ? true : false;

		if (!is_numeric($element_id))
		{
			require_once './view/error/notfound.php';
		}

		switch ($source_type)
		{
			default:
			case 'group_id':	$type = 'student'; break;
			case 'teacher_id':	$type = 'teacher'; break;
		}

		$schedule = new Timetable($element_id, $type, $force_cache_update);
		$schedule->group = trim($schedule->group);

		//var_dump($schedule); var_dump($element_id); var_dump($type); exit;

		if (!$schedule)
		{
			require_once './view/error/timeout.php';
		}
		else if (!$schedule->group)
		{
			require_once './view/error/notfound.php';
		}
		else
		{
			// Create new calendar
			if ($separate_calendar)
			{
				$calendar = new Google_Calendar();
				$calendar->setSummary('Расписание ' . trim($schedule->group));
				$calendar->setTimeZone('Europe/Samara');

				$createdCalendar = $service->calendars->insert($calendar);
				$createdCalendarId = $createdCalendar['id'];
			}

			// Or use primary
			else
			{
				$createdCalendarId = 'primary';
			}

			// Finally add data
			for ($i = 0; $i < count($schedule->schedule->weeks); $i++)
			{
				for ($j = 0; $j < count($schedule->schedule->weeks[$i]->days); $j++)
				{
					for ($k = 0; $k < count($schedule->schedule->weeks[$i]->days[$j]->lessons); $k++)
					{
						$lesson = $schedule->schedule->weeks[$i]->days[$j]->lessons[$k];

						if (!$lesson->subject)
						{
							continue;
						}

						switch ($j)
						{
							case 0: $day = 'Monday';	$eday = 'MO';	break;
							case 1: $day = 'Tuesday';	$eday = 'TU';	break;
							case 2: $day = 'Wednesday';	$eday = 'WE';	break;
							case 3: $day = 'Thursday';	$eday = 'TH';	break;
							case 4: $day = 'Friday';	$eday = 'FR';	break;
							case 5: $day = 'Saturday';	$eday = 'SA';	break;
							case 6: $day = 'Sunday';	$eday = 'SU';	break;
							default: break;
						}

						// Set date
						$date = strtotime('first ' . $day . ' of September ' . date('Y'));

						// Check for week offset
						$offset = $i;

						if (date('W', $date) % 2 == 0)
						{
							switch ($i)
							{
								case 0: $offset = 1; break;
								case 1: $offset = 0; break;
							}
						}

						$date = date('Y-m-d', strtotime(date('Y-m-d', $date) . ' +' . $offset . ' week'));

						// Create base event info
						$event = new Google_Event();
						$event->setSummary($lesson->subject);
						$event->setLocation($lesson->audience);
						$event->setDescription(($lesson->teacher ? $lesson->teacher : ''));

						// Setup start time
						$start = new Google_EventDateTime();
						$start->setDateTime($date . 'T' . $lesson->time[0] . ':00.000+04:00');
						$start->setTimeZone('Europe/Samara');
						$event->setStart($start);

						// Setup end time
						$end = new Google_EventDateTime();
						$end->setDateTime($date . 'T' . $lesson->time[1] . ':00.000+04:00');
						$end->setTimeZone('Europe/Samara');
						$event->setEnd($end);

						// Set repeat condition
						$event->setRecurrence(array('RRULE:FREQ=WEEKLY;WKST=MO;INTERVAL=2;BYDAY=' . $eday));

						// Setup time reminder if needed
						if ($k == 0 && $reminder_time != 0)
						{
							$reminder = new Google_EventReminders();
							$reminder->setUseDefault(false);
							$reminder->setOverrides(array(array('method' => 'popup', 'minutes' => $reminder_time)));

							$event->setReminders($reminder);
						}

						$createdEvent = $service->events->insert($createdCalendarId, $event);
					}
				}
			}

			// Output success
			require_once './view/success.php';
		}
	}

	$_SESSION['token'] = $client->getAccessToken();
}
else
{
	// Show connect link
	$authUrl = $client->createAuthUrl();

	require_once './view/connect.php';
}

require_once './view/footer.php';
<?php

class Schedule
{
	public $weeks = array();
}

class Week
{
	public $days = array();
}

class Day
{
	public $lessons = array();
}

class Lesson
{
	public $time, $audience, $subject, $teacher;
}

class Timetable
{
	private $time = array
	(
		array('8:00', '9:30'),
		array('9:45', '11:15'),
		array('11:30', '13:00'),
		array('13:40', '15:10'),
		array('15:20', '16:50'),
		array('17:00', '18:30'),
		array('18:40', '20:10'),
		array('20:20', '21:50')
	);

	public $schedule = array(), $group;

	public function __construct ( $element_id, $type = 'student', $force_update = false )
	{
		// Double-check
		if (!is_numeric($element_id))
		{
			return false;
		}

		// Type
		switch ($type)
		{
			default:
			case 'student':
			{
				$xml_source = 'http://rasp.sstu.ru/htmlViewRaspBook.aspx?GROUP=' . $element_id . '&OLD=00';

				$cache_file = './cache/group-' . $element_id;

				break;
			}

			case 'teacher':
			{
				$xml_source = 'http://rasp.sstu.ru/htmlViewRaspPrep.aspx?PREP=' . $element_id . '&OLD=00';

				$cache_file = './cache/teacher-' . $element_id;

				break;
			}
		}

		// Check cache
		if (is_file($cache_file) && !$force_update)
		{
			$cache = unserialize(file_get_contents($cache_file));

			$this->schedule = $cache[0];

			$this->group = $cache[1];

			return;
		}
		else
		{
			$raw_data = file_get_contents($xml_source);
		}

		// Remove unused spaces
		$decoded_data = preg_replace("/<([^> ]+)/e", "strtolower('\\0')", $raw_data);
		$decoded_data = preg_replace('/\s+/i', ' ', $decoded_data);

		// Remove tag attributes
		$decoded_data = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i", '<$1$2>', $decoded_data);

		// Convert html to xml
		$decoded_data = str_replace('&', '&amp;', $decoded_data);
		$decoded_data = str_replace(array('<br>', '<br/>'), '<br />', $decoded_data);
		$decoded_data = str_replace(array('<img/>', '<img>'), '', $decoded_data);
		$decoded_data = str_replace(array('<meta/>', '<meta>'), '', $decoded_data);
		$decoded_data = str_replace('</font><br /></font><br />', '</font><br />', $decoded_data); // Instant fix!
		$decoded_data = str_replace('<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >', '', $decoded_data);

		//var_dump($decoded_data); exit;

		// Create parser instance
		$xml = new SimpleXMLElement($decoded_data);

		/*
		 *	Collector
		 */
		$this->schedule = new Schedule();
		
		switch ($type)
		{
			// For students
			default:
			case 'student':
			{
				$this->group = trim((string) @$xml->head->title);

				for ($i = 0; $i < count($xml->body->p->table->tr[2]->td[1]->table); $i++)
				{
					$week = new Week();

					for ($j = 1; $j < count($xml->body->p->table->tr[2]->td[1]->table[$i]->tbody->tr[2]->td); $j++)
					{
						$day = new Day();

						for ($k = 2; $k < count($xml->body->p->table->tr[2]->td[1]->table[$i]->tbody->tr); $k++)
						{
							if (@$xml->body->p->table->tr[2]->td[1]->table[$i]->tbody->tr[$k]->td[$j]->div->a->b)
							{
								$lesson = new Lesson();
								$lesson->time = $this->time[$k - 2];
								$lesson->audience = trim((string) @$xml->body->p->table->tr[2]->td[1]->table[$i]->tbody->tr[$k]->td[$j]->div->a->b);
								$lesson->subject = trim((string) @$xml->body->p->table->tr[2]->td[1]->table[$i]->tbody->tr[$k]->td[$j]->div->font[0]);
								$lesson->teacher = trim((string) @$xml->body->p->table->tr[2]->td[1]->table[$i]->tbody->tr[$k]->td[$j]->div->font[1]->a);

								$lesson->subject = str_replace('(лек)', '(лек.)', $lesson->subject);
								$lesson->subject = str_replace('(прак)', '(прак.)', $lesson->subject);

								$day->lessons[] = $lesson;
							}
						}

						$week->days[] = $day;
					}

					$this->schedule->weeks[] = $week;
				}

				break;
			}

			// For teachers
			case 'teacher':
			{
				$this->group = trim(str_replace('Расписание:', '', ((string) @$xml->head->title)));

				for ($i = 0; $i < count($xml->body->div->table->tr[1]->td->table); $i++)
				{
					$week = new Week();

					for ($j = 1; $j < count($xml->body->div->table->tr[1]->td->table[$i]->tbody->tr[2]->td); $j++)
					{
						$day = new Day();

						for ($k = 2; $k < count($xml->body->div->table->tr[1]->td->table[$i]->tbody->tr); $k++)
						{
							if (@$xml->body->div->table->tr[1]->td->table[$i]->tbody->tr[$k]->td[$j]->div->a->b)
							{
								$lesson = new Lesson();
								$lesson->time = $this->time[$k - 2];
								$lesson->audience = trim((string) @$xml->body->div->table->tr[1]->td->table[$i]->tbody->tr[$k]->td[$j]->div->a->b);

								$data = trim((string) @$xml->body->div->table->tr[1]->td->table[$i]->tbody->tr[$k]->td[$j]->div->font);
								$data = explode(')', $data);
								
								$lesson->subject = $data[1] . ': ' . $data[0] . ')';

								$lesson->subject = str_replace('(лек)', '(лек.)', $lesson->subject);
								$lesson->subject = str_replace('(прак)', '(прак.)', $lesson->subject);

								$day->lessons[] = $lesson;
							}
						}

						$week->days[] = $day;
					}

					$this->schedule->weeks[] = $week;
				}

				break;
			}
		}

		@file_put_contents($cache_file, serialize(array($this->schedule, $this->group)));
	}
}
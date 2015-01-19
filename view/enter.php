<h1>Настройка параметров для синхронизации</h1>
<form method="post" action=".">
	<table>
		<tbody>
			<tr>
				<td width="220">
					<select name="source_type" onchange="update_tip_info();" id="source-type">
						<option value="group_id" selected="selected">Для студента</option>
						<option value="teacher_id">Для преподавателя</option>
					</select>
				</td>
				<td>
					<input type="text" name="element_id" id="element_id" autofocus="autofocus" pattern="(\d+)" required="required" placeholder="Номер группы"  />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<span class="info">
						<span id="tip-for-students">
							Номер группы можно найти в ссылке на расписание, например:
							<br />
							http://rasp.sstu.ru/htmlViewRaspBook.aspx?GROUP=<strong>91256</strong>&amp;OLD=00
						</span>
						<span id="tip-for-teachers" class="hidden">
							Номер преподавателя можно найти в ссылке на расписание, например:
							<br />
							http://rasp.sstu.ru/htmlViewRaspPrep.aspx?PREP=<strong>077016</strong>&amp;OLD=00
						</psan>
					</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<tr>
				<td>
					<label for="reminder_time">Напоминать о первой паре</label>
				</td>
				<td>
					<select name="reminder_time" id="reminder_time">
						<option value="0" selected>Не напоминать</option>
						<option value="15">За 15 мин.</option>
						<option value="30">За 30 мин.</option>
						<option value="60">За 1 час</option>
						<option value="90">За 1.5 часа</option>
						<option value="120">За 2 часа</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<span class="info">
						На Android устройствах уведомление выглядит как push-сообщение. На iOS &mdash; стандартное уведомление.
					</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<tr>
				<td>
					<label for="separate_calendar">Отдельным календарём</label>
				</td>
				<td>
					<input type="checkbox" name="separate_calendar" id="separate_calendar" checked="checked" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<span class="info">
						Пользователям iOS настоятельно <strong>рекомендуется убрать</strong> эту галочку, дабы избежать лишних проблем с синхронизацией.
						Причиной данного костыля является <!-- отсутствие синхронизации для отличных от главного календарей по умолчанию. --> особенность системы.
						<br />
					</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<tr>
				<td>
					<label for="force_cache_update">Обновить кеш</label>
				</td>
				<td>
					<input type="checkbox" name="force_cache_update" id="force_cache_update" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<span class="info">
						Сильно замедляет синхронизацию. Данную галочку стоит отметить в случае изменения в расписании.
						<br />
						Все результаты с сервера rasp.sstu.ru кешируются.
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<input type="submit" class="action" value="Синхронизировать"  />
				</td>
			</tr>
		</tbody>
	</table>
</form>
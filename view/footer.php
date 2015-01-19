			<div id="footer">
				<a href="https://play.google.com/store/apps/details?id=com.google.android.calendar">Приложение для Android</a>
				&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
				<a href="https://support.google.com/calendar/answer/151674?hl=ru">Инструкция для iOS</a>
				&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
				<a href="https://www.google.com/calendar/render">Веб интерфейс</a>
				<?php if ($client->getAccessToken()): ?>
					&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
					<a href="?logout">Выход</a>
				<?php endif; ?>
			</div>
		</div>
	</body>
</html>
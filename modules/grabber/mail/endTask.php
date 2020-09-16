<p>Сканирование завершилось удачно. Добавлено <?= $count ?> записи(ей)</p>
<p>Задача:<?= $taskTitle ?></p>
<p>Источник:<?= $source ?></p>
<p>
	Добавленные записи:
	<ul>
		<?php foreach ( $records as $record ): ?>
			<li><a href="<?= $record['link'] ?>"><?= $record[ 'title' ] ?></a></li>
		<?php endforeach; ?>
	</ul>
</p>
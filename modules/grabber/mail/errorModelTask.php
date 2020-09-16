<p>Сканирование завершилось неудачно.</p>
<p>Задача:<?= $taskTitle ?></p>
<p>Источник:<?= $source ?></p>
<p>
	<h1>Ошибки</h1>
	<br>
	<ul>
		<?php foreach ( $errors as $attributeName => $attributeErrors ): ?>
			<li><?= $attributeName ?></li>
			<ul>
				<?php foreach ( $attributeErrors as $errorText ): ?>
					<li><?= $errorText ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</ul>
</p>
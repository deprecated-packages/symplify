<header>
	<?php if($this->item && !empty($this->item->name)): ?>
		<h1><?= nl2br($this->escape($this->item->name)) ?></h1>
		<?php $this->headTitle()->append($this->item->name) ?>
	<?php else: ?>
		<h1><?= $this->escape($this->category->name) ?></h1>
	<?php endif; ?>
	<hr />
</header>
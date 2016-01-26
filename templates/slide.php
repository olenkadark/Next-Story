<nav class="nav-slide u_next_story">
	<?php previous_post_link('<div class="prev">%link</div>', '<span class="icon-wrap"><svg class="icon" width="32" height="32" viewBox="0 0 64 64"><use xlink:href="#arrow-left-1"></svg></span>
		<div>
			<h3>%title <span>by %author</span></h3>
			%thumb
		</div>'); ?> 	
	<?php next_post_link( '<div class="next">%link</div>', '<span class="icon-wrap"><svg class="icon" width="32" height="32" viewBox="0 0 64 64"><use xlink:href="#arrow-right-1"></svg></span>
		<div>
			<h3>%title <span>by %author</span></h3>
			%thumb
		</div>' ); ?>		
</nav>
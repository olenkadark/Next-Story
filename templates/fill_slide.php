<nav class="nav-fillslide u_next_story">
	<?php previous_post_link('<div class="prev">%link</div>', '<span class="icon-wrap"><svg class="icon" width="24" height="24" viewBox="0 0 64 64"><use xlink:href="#arrow-left-4"></svg></span>
		<div>
			<h3>%title</h3>
			<span>by %author</span>
			%thumb
		</div>'); ?> 	
	<?php next_post_link( '<div class="next">%link</div>', '<span class="icon-wrap"><svg class="icon" width="24" height="24" viewBox="0 0 64 64"><use xlink:href="#arrow-right-4"></svg></span>
		<div>
			<h3>%title</h3>
			<span>by %author</span>
			%thumb
		</div>' ); ?>		
</nav>
<nav class="nav-growpop u_next_story">
	<?php previous_post_link('<div class="prev">%link</div>', '<span class="icon-wrap"><svg class="icon" width="24" height="24" viewBox="0 0 64 64"><use xlink:href="#arrow-left-2"></svg></span>
		<div>
			<span>Next Story</span>
			<h3>%title</h3>
			<p>by %author</p>
			%thumb100
		</div>'); ?> 	
	<?php next_post_link( '<div class="next">%link</div>', '<span class="icon-wrap"><svg class="icon" width="24" height="24" viewBox="0 0 64 64"><use xlink:href="#arrow-right-2"></svg></span>
		<div>
			<span>Next Story</span>
			<h3>%title</h3>
			<p>by %author</p>
			%thumb100
		</div>' ); ?>		
</nav>
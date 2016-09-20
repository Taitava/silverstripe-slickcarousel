<% if $Link %>
	<a href="$Link.XML">
<% end_if %>
	
<div class="slick-carousel-slide"<% if $StyleAttribute %> style="$StyleAttribute"<% end_if %>>
	<% if $ImagePlacement = 'before-content' %>$Image<% end_if %>$Content<% if $ImagePlacement = 'after-content' %>$Image<% end_if %>
</div>

<% if $Link %>
	</a>
<% end_if %>
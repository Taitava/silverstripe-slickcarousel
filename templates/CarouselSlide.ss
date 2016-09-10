<% if $Link %>
	<a href="$Link.XML">
<% end_if %>
	
<div class="slick-carousel-slide" style="background-image: url('$Image.Link.XML');">
	$Content
</div>

<% if $Link %>
	</a>
<% end_if %>
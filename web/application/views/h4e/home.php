<div class="tabbable">
	<div class="row">
		<div class="span12">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_feed" data-toggle="tab">Read</a></li>
				<li><a href="#tab_media" data-toggle="tab">View</a></li>
				<li><a href="#tab_map" data-toggle="tab">Visit</a></li>
			</ul>
		</div>
	</div>
</div>
<div class="tab-content">
	<!-- #tab_feed -->
	<div class="tab-pane active" id="tab_feed">
		<div class="row">
			<div class="span12">
				<div class="pagination"></div>
			</div>
		</div>
		<div class="row" id="tab_feed_content">
			<div id="feeditem_container" class="span6">
				<div id="feed_directory"></div>
			</div>
			<div id="reaction_container" class="span6 scroll-top">
				<div id="reaction_directory"></div>		
			</div>
		</div>
	</div><!-- #tab_feed -->

	<!-- #tab_media -->
	<div class="tab-pane" id="tab_media">
		<div class="row">
			<div class="span12">
				<div id="media_directory"></div>
			</div>
		</div>
	</div><!-- #tab_media -->

	<!-- #tab_map -->
	<div class="tab-pane" id="tab_map">
		<div class="row">
			<div id="map_container" class="span12">
			</div>
		</div>
	</div><!-- #tab_map -->

</div>
<?php $this->load->view('modal_permalink'); ?>
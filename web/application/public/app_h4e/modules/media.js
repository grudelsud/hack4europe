(function(Media) {

	Media.Model = Backbone.Model.extend({});

	Media.Collection = Backbone.Collection.extend({
		model: Media.Model,
		url: base_url + 'index.php/h4ejson/media',
		parse: function(response) {
			return response.success;
		}
	});

	Media.Views.Main = Backbone.View.extend({
		el: '#media_directory',
		initialize: function() {
			this.collection.on('reset', this.render, this);
			this.collection.on('change', this.render, this);
		},
		render: function() {
			var view = this;
			view.$el.empty().append('<ul class="thumbnails"></ul>');
			_.each(this.collection.models, function(media) {
				view.$el.find(':first').append('<li class="span2"><a href="#" class="thumbnail"><img src="'+media.get('url')+'" /></a></li>');
			});
		}
	});

/**
	<ul class="thumbnails">
	<% _.each(media.models, function(item) { %>
		<li class="span2">
		<a href="<%= item.get('url') %>" class="thumbnail">
		<% if(item.get('type') == 'image') { %>
		<img src="<%= item.get('url') %>">
		<% } else if(item.get('type') == 'video') { %>
		<i class="icon-film"></i>
		<% } %>
		</a>
		</li>
	<% }); %>	
	</ul>
 */

})(readreactv.module('media'));
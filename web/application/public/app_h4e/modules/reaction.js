(function(Reaction) {

	// dependency
	var Tag = readreactv.module('tag');
	var Media = readreactv.module('media');
	var Europeana = readreactv.module('europeana');

	Reaction.Model = Backbone.Model.extend({
		defaults: {
			tags: new Tag.Collection(),
			media: new Media.Collection(),
			locations: new Tag.Collection(),
			people: new Tag.Collection(),
			organizations: new Tag.Collection(),
		},
		urlRoot: base_url + 'index.php/h4ejson/reactions/id',
		parse: function(response) {
			var result = response.success;
			var content = result.content;
			this.set({
				tags: new Tag.Collection(result.tags),
				media: new Media.Collection(result.media),
				locations: new Tag.Collection(result.entities.locations),
				people: new Tag.Collection(result.entities.people),
				organizations: new Tag.Collection(result.entities.organizations),
			});
			return content;
		}
	});

	Reaction.Views.Main = Backbone.View.extend({
		template: assets_url+'app_h4e/templates/reaction.html',
		templateTweet: assets_url+'app_h4e/templates/tweet.html',
		el: '#reaction_directory',
		events: {
			'click .label': 'tagSelect',
			'click #europeana_content': 'showEuropeana'
		},
		initialize: function() {
			this.model.on('change', this.render, this);
			this.loadingEuropeana = false;
		},
		empty: function() {
			var view = this;
			view.$el.empty();
		},
		render: function() {
			var view = this;
			// Fetch the template, render it to the View element and call done.
			if(typeof this.model.get('permalink') !== 'undefined') {
				readreactv.fetchTemplate(this.template, function(tmpl) {
					view.$el.html(tmpl(view.model.toJSON()));
				});
			}
			return this;
		},
		tagSelect: function(e) {
			var $label = $(e.target);
			var tag_id = $label.attr('data-id');
			var tag_type = $label.attr('data-type');
			var tag_obj = this.model.get(tag_type).get(tag_id);

			$label.toggleClass('label-success');
			if($label.hasClass('label-success')) {
				tag_obj.set({selected: true});
			} else {
				tag_obj.set({selected: false});
			}
			this.loadEuropeana();
		},
		loadEuropeana: function() {
			this.loadingEuropeana = true;
			var query_terms = [];

			var selected_tags = this.model.get('tags').where({selected: true});
			_.each(selected_tags, function(tag) { query_terms.push(encodeURI(tag.get('name'))); });

			var selected_people = this.model.get('people').where({selected: true});
			_.each(selected_people, function(tag) { query_terms.push(encodeURI(tag.get('name'))); });

			var selected_organizations = this.model.get('organizations').where({selected: true});
			_.each(selected_organizations, function(tag) { query_terms.push(encodeURI(tag.get('name'))); });

			var selected_locations = this.model.get('locations').where({selected: true});
			_.each(selected_locations, function(tag) { query_terms.push(encodeURI(tag.get('name'))); });

			var europeana = new Europeana.Model({query: query_terms.join('+')});
			var view = this;
			europeana.fetch({
				success: function(result) {
					var $list = $('<ul></ul>');
					_.each(result.get('items'), function(item) {
						var ext_info  = 'type: '+item['europeana:type']+', year: '+item['europeana:year']+', provided by: '+item['europeana:provider'];
						$list.append('<li><a href="#" data-link="'+item.link+'">'+item.title+'</a> <i class="icon-info-sign"></i> '+ext_info+'</li>');
					});
					view.$el.find('#europeana_content').empty().append('<h3>Europeana Results</h3>').append($list);
					view.loadingEuropeana = false;
				}
			});
		},
		showEuropeana: function(e) {
			e.preventDefault();
			var $link = $(e.target);

			var data = {};
			data.link = $link.attr('data-link')
			$.post(base_url + 'index.php/h4ejson/eu_fetch', data, function(data) {
				var content = '';
				if( data.success != 'undefined' ) {
					content += '<h3>'+data.success['dc:title']+'</h3>';
					if(data.success['europeana:type'] == 'IMAGE' || data.success['europeana:type'] == 'VIDEO') {
						content += '<img src="'+data.success['europeana:object']+'" />'
					}
					if( data.success['europeana:isShownAt'] != 'undefined' ) {
						content += '<p><a href="'+data.success['europeana:isShownAt']+'">view source</a></p>';						
					}
					content += '<p>provided by: '+data.success['dc:source']+'</p>';
					content += '<p>language: '+data.success['europeana:language']+'</p>';
					content += '';
				} else {
					content += 'uhm...';
				}
				$('#permalink_content').empty().append(content);
				$('#permalink_container').modal('show');
			});
		}
	});

})(readreactv.module('reaction'));